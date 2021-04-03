<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use App\Transaction;
use App\Ticket;
use App\Bet;
use App\Outlet;
use App\Game;
use App\User;
use App\Price;
use GeneratorHelper;
use BetHelper;
use CreditsHelper;
use Illuminate\Database\Eloquent\Collection;
use App\Events\TicketCreated;
use App\Area;
use App\Betlimit;
use App\Helpers\TicketHelper;

class TransactionController extends Controller
{

    public function getTickets(Request $request)
    {
        $user = $request->user();
        $date_from = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $date_to = $request->filled('date_to') ? Carbon::parse($request->date_to) : $date_from;
        $draw_time = $request->filled('draw_time') ? $request->draw_time : '';
        $game = $request->filled('game') ? $request->game : '';
        $ticket_num = $request->filled('ticket_number') ? $request->ticket_number : '';
        $is_own = $request->filled('is_own') ? true : false;
        $agents = $request->filled('user') ? (array)$request->user : null;

        $ticketsHelper = new TicketHelper;
        $tickets = $ticketsHelper->getTickets($date_from, $date_to, $draw_time, $user, $is_own, true, $game, $ticket_num, $agents);

        return $tickets;

    }

    public function get(Request $request)
    {
        $tickets = null;
        $user = $request->user();
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $is_own = $request->filled('is_own') ? true : false;
        $ticket_search = '';

        $where = array('is_cancelled' => false);

        if( $request->filled('draw_time') )
            $where['draw_time'] = $request->draw_time;
        if ($request->filled('game'))
            $where['game_id'] = $request->game;
        if ($request->filled('ticket_number'))
            $ticket_search = $request->ticket_number;

        if ($user->role == 'super_admin') {
            $_coordinators = User::where('role', 'coordinator')->get();
            $agents = User::where('role', 'usher')
                ->orWhere('role', 'teller')
                ->get();
            $agents = $agents->merge($_coordinators);
        } elseif ( $user->role == 'area_admin' ) {
            $area = $user->areas()->first();
            $agents = $area->areaAgents()->get();
            $coordinators = $user->children()->where('role', 'coordinator')->get();
            $agents = $agents->merge( $coordinators );
        } elseif ( $user->role == 'coordinator' ) {
            if( $is_own ){
                $agents = User::where('id', $user->id)->get();
            }else{
                $agents = $user->children;
                $agents->push($user);
            }
        } elseif ( $user->role == 'usher' || $user->role == 'teller' ) {
            $agents = User::where('id', $user->id)->get();
        }

        $tickets = new Collection();
        foreach ($agents as $agent) {
            foreach ($agent->transactions as $transaction) {
                if( $transaction->total_amount == 0 )
                    continue;
                // filter tickets
                $filtered_tickets = $transaction->tickets()
                    ->where($where)
                    ->where('ticket_number', 'LIKE', '%'.$ticket_search.'%')
                    ->whereDate('draw_date', $date)
                    ->get();
                foreach ($filtered_tickets as $ticket) {
                    $_ticket = Ticket::select('id', 'ticket_number', 'draw_date', 'draw_time', 'total_amount', 'created_at')
                        ->where('id', $ticket->id)->first();
                    $_ticket->bets = Bet::where('ticket_id', $ticket->id)
                        ->pluck('id', 'combination');
                    $_ticket->user = User::select('firstname', 'lastname')
                        ->where('id', $transaction->user_id)
                        ->with('outlets.name')
                        ->first();
                    $_ticket->txn_code = $transaction->code;
                    $_ticket->txn_id = $transaction->id;
                    $tickets->push($_ticket);
                }
            }
        }
        return $tickets->sortByDesc('id')->values();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $draw_time = request('tickets')[0]['draw_time'];

        // fake time
        if( $draw_time == 11 )
            $actual_time = 14;
        elseif( $draw_time == 16 )
            $actual_time = 17;
        elseif( $draw_time == 21 )
            $actual_time = 21;

        if (in_array(auth()->user()->role, ['area_admin', 'super_admin', 'coordinator'])) {
            $area_details = auth()->user()->areas()->first() ?: Area::first();
            $cutoff_time = optional($area_details)->cutoff_time ?: 15;
        } elseif (in_array(auth()->user()->role, ['teller', 'usher', 'player'])) {
            $coordinator = auth()->user()->parent()->allRelatedIds()->first();
            $cutoff_time = optional(User::find($coordinator)->areas->first())->cutoff_time ?: 15;
        }


         // fake time - xmas and new year eve
        // if( $draw_time == 11 ){
        //     $actual_time = 12; //12PM
        //     $default_cutoff = 45; //co:11:15
        // }elseif( $draw_time == 16 ){
        //     $actual_time = 13; //1PM
        //     $default_cutoff = 45; //co:12:15 
        // }elseif( $draw_time == 21 ){
        //     $actual_time = 14; //2PM
        //     $default_cutoff = 30; //co:1:30
        // }

        // if (in_array(auth()->user()->role, ['area_admin', 'super_admin', 'coordinator'])) {
        //     $area_details = auth()->user()->areas()->first() ?: Area::first();
        //     $cutoff_time = optional($area_details)->cutoff_time ?: $default_cutoff;
        // } elseif (in_array(auth()->user()->role, ['teller', 'usher'])) {
        //     $coordinator = auth()->user()->parent()->allRelatedIds()->first();
        //     $cutoff_time = optional(User::find($coordinator)->areas->first())->cutoff_time ?: $default_cutoff;
        // }
        // @end fake time - xmas and new year eve

        if(Carbon::now()
            > Carbon::now()
            ->hour($actual_time)
            ->minute(0)
            ->seconds(0)
            ->subMinutes($cutoff_time)
        ) {
            return response(['cut_off' => true], 422);
        }

        // init variables and dynamic data
        $txn_request = $request->json()->all();
        $bet_limit = 15;
        $txn_total_amount = 0;
        $sold_out_list = array();
        $created_at = array_key_exists('created_at', $txn_request) ? $txn_request['created_at'] : Carbon::now();
        $txn_code = array_key_exists('code', $txn_request) ? $txn_request['code'] : GeneratorHelper::generateTxnCode();

        // check if tnx_code exists and has valid tickets
        if( Transaction::where('code', $txn_code)->first() ){
            return array(
                'success' => false,
                'error_code' => 'txn_exists',
                'message' => 'Transaction already exists.',
            );
        }

        if( $request->user()->role == 'super_admin' )
            $agent = User::find($txn_request['user_id']);
        else
            $agent = $request->user();

        // check if has sufficient credits
        $total_bet_payable = 0;
        $activate_credits = $agent->activate_credit;
        
        // &optimize
        if( $activate_credits ){
            $total_bet_payable = CreditsHelper::getBetTotalPayable($txn_request['tickets']);
            $credits_bal = $agent->creditBalance();
            if( $credits_bal < $total_bet_payable ){
                return array(
                    'success' => false,
                    'error_code' => 'insufficient_credits',
                    'message' => 'Insufficient credits.',
                );
            }
        }

        // get winning amount in area
        try {
            if (in_array($agent->role, array('usher', 'teller', 'player'))) {
                $area_id = $agent->agentAreas()->allRelatedIds()->first();
                $channel = $agent->parent()->first();
            } else {
                $area_id = $agent->areas()->allRelatedIds()->first();
                $channel = $agent;
            }

            $area = Area::find($area_id);
            $admin_channel = User::where('role', 'super_admin')->first();
            $area_admin_channel = $area->users()->where('role', 'area_admin')->first();

            $area_price = Price::select('price_per_unit', 'bet_limit')
                ->where([
                    'area_id' => $area_id,
                    'game_id' => $txn_request['game_id']
                ])->get()->first();
            $bet_limit = $area_price->bet_limit;
        } catch (\Throwable $th) {
            return array(
                'success' => false,
                'error_code' => 'price_details_error',
                'message' => 'Something went wrong on retrieving price details for the area',
            );
        }

        // create Transaction
        try {
            $transaction = new Transaction();
            // $transaction->code = GeneratorHelper::generateTxnCode();
            $transaction->code = $txn_code;
            $transaction->game_id = $txn_request['game_id'];
            $transaction->user_id = $agent->id;
            $transaction->created_at = $created_at;
            $transaction->save();
        } catch (\Throwable $th) {
            return array(
                'success' => false,
                'error_code' => 'txn_save_error',
                'message' => 'Something went wrong on saving transaction',
            );
        }

        // loop to each draw_time group. 1 draw_time = 1 ticket
        foreach ($txn_request['tickets'] as $_ticket) {

            try {
                $ticket = new Ticket();
                // $ticket->ticket_number = GeneratorHelper::generateTicketNum();
                if( array_key_exists('ticket_number', $txn_request) )
                    $ticket->ticket_number = $_ticket['ticket_number'];
                else
                    $ticket->ticket_number = GeneratorHelper::generateTicketNum();

                $ticket->created_at = $created_at;
                $ticket->draw_time = $_ticket['draw_time'];
                $ticket->draw_date = Carbon::parse($_ticket['draw_date']);
                $ticket->transaction_id = $transaction->id;
                $ticket->game_id = $transaction->game_id;
                $ticket->area_id = $area_id;
                $ticket->is_credit = $activate_credits;
                $ticket->save();
            } catch (\Throwable $th) {
                // should we delete transaction here
                return array(
                    'success' => false,
                    'error_code' => 'ticket_save_error',
                    'message' => 'Something went wrong on saving ticket for transaction: '.$transaction->code,
                );
            }

            // loop to each bets in draw_time group and add to bet
            $ticket_total_amount = 0;
            foreach ( $_ticket['bets'] as $_bet ) {
                $bet_amount = (int) $_bet['amount'];

                // check if bet combination is available or sold out. amount = 0 id sold out
                $check_sold_out = true;
                if( $check_sold_out ){
                    $ramble_occurence_amount = 0;
                    $where = array('tickets.is_cancelled' => false);
                    $where['tickets.area_id'] = $area_id;
                    $where['tickets.draw_time'] = $_ticket['draw_time'];
                    $whereDate = Carbon::parse($_ticket['draw_date']);

                    if( $_bet['type'] == 'straight' ){
                        $possible_combinations = [$_bet['combination']];
                    }else{
                        $possible_combinations = BetHelper::getCombinationsOfRamble($_bet['combination']);
                    }

                    foreach ($possible_combinations as $_combi) {
                        $__bet_amount = $bet_amount;
                        $hn_bet_limit = $area->limit ? $area->limit : env('DEFAULT_BET_LIMIT', 200);
                        $_bet_limit = Betlimit::where('combination', $_combi)
                            ->where('area_id', $area_id)
                            ->first();
                            
                        if ($_bet_limit && $_bet_limit->limit > 0) {
                            $hn_bet_limit = $_bet_limit->limit;
                        }

                        $target_total_amount = BetHelper::getTargetTotalAmount($_combi, $where, $whereDate);

                        $rambled_array = BetHelper::getCombinationsOfRamble($_combi);
                        $ramble_occurence_amount = BetHelper::getRambleTotalAmount($rambled_array, $where, $whereDate);
                        $ramble_occurence_amount = $ramble_occurence_amount / count($rambled_array);

                        $combi_total_amount = $target_total_amount + $ramble_occurence_amount;

                        if ($_bet['type'] == 'ramble') {
                            $__bet_amount = $bet_amount / count($rambled_array);
                        }

                        // check if sold out
                        if (($combi_total_amount + $__bet_amount) > $hn_bet_limit) {
                            $bet_amount = 0;
                            // add to sold out object
                            array_push($sold_out_list, array(
                                'combination' => $_combi,
                                'bet_limit' => $hn_bet_limit,
                                'current_total_amount' => $combi_total_amount,
                                'available' => $hn_bet_limit - $combi_total_amount
                            ));
                            break;
                        }
                    }

                } //@end check_sold_out

                $ticket_total_amount += $bet_amount;

                // price per unit
                $_price_per_unit = (int) $area_price->price_per_unit; 
                $combi_data = Betlimit::where('combination', $_bet['combination'])
                            ->where('area_id', $area_id)
                            ->first();
                if ($combi_data && $combi_data->winning > 0) {
                    $_price_per_unit = $combi_data->winning;
                }

                // check if auto-divide is enabled
                $auto_divide = env('ENABLE_AUTODIVIDE', false);
                if( !$auto_divide ){
                    $this->saveBet($_bet, $bet_amount, $_price_per_unit, $transaction->id, $ticket->id);
                    // $ticket_total_amount += $bet_amount;
                    continue;
                }

                // for ramble ==============================
                $ramble_combinations_count = count(BetHelper::getCombinationsOfRamble($_bet['combination']));
                $bet_amount_per_combination = $bet_amount / $ramble_combinations_count;
                // for ramble ==============================

                if( $_bet['type'] == 'straight' && $bet_amount > $bet_limit ){

                    while ( $bet_amount > 0) {
                        $_amount = $bet_limit;
                        if ( $bet_amount <= $bet_limit)
                            $_amount = $bet_amount;
                        $this->saveBet($_bet, $_amount, $_price_per_unit, $transaction->id, $ticket->id);
                        $bet_amount -= $bet_limit;
                    }

                }elseif ($_bet['type'] == 'ramble' && $bet_amount_per_combination > $bet_limit) {

                    while ($bet_amount > 0) {
                        $_amount = $bet_limit * $ramble_combinations_count;
                        if ($bet_amount <= $_amount)
                            $_amount = $bet_amount;
                        $this->saveBet($_bet, $_amount, $_price_per_unit, $transaction->id, $ticket->id);
                        $bet_amount -= $_amount;
                    }

                }else{
                    $this->saveBet($_bet, $bet_amount, $_price_per_unit, $transaction->id, $ticket->id);
                }

                // $ticket_total_amount += $bet_amount;

            } // @end foreach bets

            // save ticket total amount
            $ticket->total_amount = $ticket_total_amount;
            $ticket->save();

            $txn_total_amount += $ticket->total_amount;
        } //@end foreach ticket

        $transaction->total_amount = $txn_total_amount;
        $transaction->save();

        $bet_response = array(
            'success' => true,
            'transaction' => Transaction::with('tickets.bets', 'game', 'user')
                ->where('id', $transaction->id)->first(),
            'sold_out_list' => $sold_out_list,
            'area' => $area->name
        );

        // deduct credits
        if( $activate_credits ){
            $agent->credits -= $total_bet_payable;
            $agent->save();
        }

        // broadcast to coordinators view
        if( env('ENABLE_BROADCAST', true) ){
            foreach ($transaction->tickets as $_ticket) {
                event( new TicketCreated( $_ticket, $channel ) );
                event( new TicketCreated( $_ticket, $admin_channel ) );
                // event( new TicketCreated( $ticket, $area_admin_channel ) );
            }
        }

        return $bet_response;
    }

    public function saveBet($bet_data, $bet_amount, $area_price, $transaction_id, $ticket_id)
    {
        $bet = new Bet();
        $bet->combination = $bet_data['combination'];
        $bet->type = $bet_data['type'];
        $bet->amount = $bet_amount;
        $winning_amount = BetHelper::getWinningAmount( $bet_data, $bet_amount, $area_price );
        $bet->winning_amount = $winning_amount;
        $bet->transaction_id = $transaction_id;
        $bet->ticket_id = $ticket_id;
        $bet->save();
        return $bet;
    }

    public function getTransaction($transaction)
    {
        $transaction = Transaction::with('user', 'game')
            ->where('id', $transaction)->first();

        if( empty( $transaction ) )
            return null;

        $bets = new Collection();
        foreach ($transaction->tickets as $ticket) {
            $bets = $bets->merge($ticket->bets()->with(array('ticket' => function ($q) {
                $q->select('id', 'ticket_number', 'draw_time', 'draw_date');
            }))->get());
        }
        $transaction->bets = $bets;

        if ($transaction->user->role == 'teller')
            $transaction->outlet = $transaction->user->outlets()->first();

        return $transaction;
    }
}
