<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Transaction;
use Carbon\Carbon;
use App\User;
use App\Winner;
use App\Ticket;
use App\Bet;
use App\Game;
use App\Area;
use App\Betlimit;
use Illuminate\Support\Facades\DB;
use App\Helpers\TicketHelper;
use App\Helpers\WinnersHelper;
use App\Price;
use BetHelper;
use App\Helpers\SalesHelper;

class ReportsController extends Controller
{

    public function getCoordinatorsTotalSales(Request $request) {   
        $user = $request->user();
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $date_to = $request->filled('date_to') ? Carbon::parse($request->date_to) : Carbon::now();

        if( $user->role == 'super_admin' ){
            if( $request->filled('area') ){
                $area_coordinators = Area::find($request->area)->users->where('role', 'coordinator')->pluck('id');
                $coordinators_query = User::whereIn('id', $area_coordinators);
            }else{
                $coordinators_query = User::where('role', 'coordinator');
            }
        }elseif( $user->role == 'area_admin' ){
            $area_coordinators = $user->areas()->first()->users->where('role', 'coordinator')->pluck('id');
            $coordinators_query = User::whereIn('id', $area_coordinators);
        }else{
            return [];
        }

        // search filter here
        if ($request->filled('coordinator')) {
            $search = $request->coordinator;
            $coordinators_query->where(function ($query) use ($search) {
                $query->where('firstname', 'LIKE', '%' . $search . '%');
                $query->orWhere('lastname', 'LIKE', '%' . $search . '%');
                $query->orWhere('email', 'LIKE', '%' . $search . '%');
                $query->orWhere('id_number', 'LIKE', '%' . $search . '%');
            });
        }

        $coordinators = $coordinators_query->get();

        $coor_sales = new Collection();
        foreach($coordinators as $coor){
            $salesHelper = new SalesHelper;
            $_coor = User::select('firstname','lastname','id','is_active', 'percentage', 'float')
                ->where('id', $coor->id)
                ->first();
            
            $_coor->area = $coor->areas()->pluck('name')->first();

            // gross
            // $_coor->sales_total = TicketHelper::getTicketSalesUnderCoordinatorPerTime($agents, $date);
            $_coor->sales_total = $salesHelper->getGrossSales($date, $date_to, '', $coor)['gross_sales'];
            // winnings
            $_coor->hits = $salesHelper->getWinnings($date, $date_to, '', $coor);

            $coor_sales->push($_coor);
        }

        return $coor_sales;
    } // @end getCoordinatorsTotalSales

    // HELPER

    public function getAreaTotalSales(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $game = '';
        $where = array('is_cancelled' => false);

        if( $request->filled('game') ){
            $where['game_id'] = $request->game;
            $game = $request->game;
        }

        $areas = Area::select('id', 'name')->get();
        
        $sales = new Collection();
        foreach($areas as $area){
            $_tickets = $area->tickets()->where($where)->whereDate('draw_date', $date)->get();

            $_winners = Winner::whereDate('created_at', $date)
                ->whereValid(true)
                ->with('bet', 'ticket')->get()
                ->filter(function ($value, $key) use ($area, $game) {
                    if($game <> '')
                        return $value->ticket->area_id == $area->id && $value->ticket->game_id == $game;
                    else
                        return $value->ticket->area_id == $area->id;
                });

            $_area = array();
            $_area['details'] = $area;
            
            $_area['gross']['11am'] = TicketHelper::getTicketsTotalSalesByTime($_tickets, 11); 
            $_area['gross']['4pm'] = TicketHelper::getTicketsTotalSalesByTime($_tickets, 16); 
            $_area['gross']['9pm'] = TicketHelper::getTicketsTotalSalesByTime($_tickets, 21); 

            $_area['wins']['11am'] = WinnersHelper::getTotalWinningsPerDrawTime($_winners, 11);
            $_area['wins']['4pm'] = WinnersHelper::getTotalWinningsPerDrawTime($_winners, 16);
            $_area['wins']['9pm'] = WinnersHelper::getTotalWinningsPerDrawTime($_winners, 21);

            $_area['hits']['11am'] = WinnersHelper::getTotalHitsPerDrawTime($_winners, 11);
            $_area['hits']['4pm'] = WinnersHelper::getTotalHitsPerDrawTime($_winners, 16);
            $_area['hits']['9pm'] = WinnersHelper::getTotalHitsPerDrawTime($_winners, 21);

            $_area['net']['11am'] = $_area['gross']['11am'] - $_area['wins']['11am'];
            $_area['net']['4pm'] = $_area['gross']['4pm'] - $_area['wins']['4pm'];
            $_area['net']['9pm'] = $_area['gross']['9pm'] - $_area['wins']['9pm'];

            $sales->push($_area); 
        }

        return $sales;
    }

    public function getHotNumbersSummary(Request $request){
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $draw_time = $request->input('draw_time', '');
        $game = $request->input('game', '');

        $bets_query = DB::table('tickets')
            ->join('bets', 'tickets.id', '=', 'bets.ticket_id')
            ->select('bets.id', 'bets.ticket_id', 'bets.combination', 'bets.type', 'amount')
            // ->select( 'bets.combination', DB::raw('sum(bets.amount) total'))
            ->groupBy('bets.combination')
            ->whereDate('tickets.draw_date', $date)
            ->where('bets.amount', '>',  0);

        if( $draw_time )
            $bets_query->where('tickets.draw_time', $draw_time);
        if( $game  )
            $bets_query->where('tickets.game_id', $game);

        return $bets_query->get();

    }

    public function getHotNumbers(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $where = array( 'tickets.is_cancelled' => false );
        $draw_time = $request->input('draw_time', '');
        $is_for_dashboard = $request->input('is_for_dashboard', false);

        if ($request->filled('game'))
            $where['tickets.game_id'] = $request->game;
        if ($draw_time)
            $where['tickets.draw_time'] = $request->draw_time;

        $bets_query = DB::table('tickets')
            ->join('bets', 'tickets.id', '=', 'bets.ticket_id')
            ->select('bets.id', 'bets.ticket_id', 'bets.combination', 'bets.type', 'amount')
            ->whereDate('tickets.draw_date', $date)
            ->where('bets.amount', '>',  0)
            ->where($where);

        // filter here
        if ($request->filled('combination')) {
            $combination = $request->combination;
            $rambled_array = BetHelper::getCombinationsOfRamble($combination);
            $bets_query->where('bets.combination', $combination);

            $bets_query->orWhere(function ($query) use ($rambled_array, $date, $draw_time) {
                $query->whereIn('bets.combination', $rambled_array);
                $query->whereDate('tickets.draw_date', $date);
                $query->where('tickets.is_cancelled', false);
                $query->where('bets.type', 'ramble');
                if( $draw_time )
                    $query->where('tickets.draw_time', $draw_time);
            });
        }

        $bets_grouped = $bets_query->get()->groupBy('combination');

        $area = $this->getRequestorArea($request->user());
        $hot_numbers = new Collection();
        foreach ($bets_grouped as $combination => $details) {
            $typeGrouped = $details->groupBy('type');

            if( $is_for_dashboard ){ //temp
                foreach ($typeGrouped as $type => $_details) {
                    $total_amount = $_details->sum('amount');
                    if ($type == 'ramble') {
                        $combinations = BetHelper::getCombinationsOfRamble($combination);
                        $total_amount = $total_amount / count($combinations);
                    }
                    $hot_numbers->push(
                        array(
                            'combination'   => $combination,
                            'type'          => $type,
                            'total_amount'  => is_float($total_amount) ? (float)number_format((float)$total_amount, 1, '.', '') : $total_amount,
                        )
                    );
                }
            }else{
                foreach ($typeGrouped as $type => $_details) {
                    $ramble_occurence_amount = 0;
                    $ramble_raw_amount = 0;
                    $total_amount = $_details->sum('amount');
                    $bet_count =  $_details->groupBy('ticket_id')->count();

                    // get bet limit
                    $bet_limit = $area->limit ? $area->limit : env('DEFAULT_BET_LIMIT', 200);
                    $_bet_limit = Betlimit::where('combination', $combination)->first();
                    if( $_bet_limit ){
                        $bet_limit = $_bet_limit->limit;
                    }

                    if ($type == 'ramble') {
                        $combinations = BetHelper::getCombinationsOfRamble($combination);
                        $ramble_raw_amount = $total_amount;
                        $total_amount = $ramble_raw_amount / count($combinations);
                    }else{
                        // get ramble counter part amount and add to target total 
                        $rambled_array = BetHelper::getCombinationsOfRamble($combination);
                        $ramble_occurence_amount = BetHelper::getRambleTotalAmount($rambled_array, $where, $date);
                        $ramble_occurence_amount = $ramble_occurence_amount / count($rambled_array);
                    }

                    $hot_numbers->push(
                        array(
                            'combination'   => $combination,
                            'type'          => $type,
                            'total_amount'  => is_float($total_amount) ? (float)number_format((float)$total_amount, 1, '.', '') : $total_amount,
                            'bet_count'     => $bet_count,
                            'bet_limit'     => $bet_limit,
                            'ramble_occurence_amount' => $ramble_occurence_amount,
                            'ramble_raw_amount' => $ramble_raw_amount
                        )
                    );
                    
                }
            } //@end if is_for_dashboard
            
        }
        
        return $hot_numbers;
    }

    public function getHotNumbersControl(Request $request)
    {   
        $user = $request->user();
        $date_from = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $date_to = $request->filled('date_to') ? Carbon::parse($request->date_to) : $date_from;
        $draw_time = $request->filled('draw_time') ? $request->draw_time : '';
        $combination = $request->filled('combination') ? $request->combination : '';

        $hot_numbers = BetHelper::getHotumbersControl($user,$date_from,$date_to,$draw_time,$combination,'');

        return $hot_numbers;
    }

    public function getHighestBets(Request $request)
    {
        $user = $request->user();
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $amount_filter = $request->filled('amount_filter') ? $request->amount_filter : 100;
        $where = array(
            'is_cancelled' => false
        );
        if( $request->filled('game_filter') )
            $where['game_id'] = $request->game_filter;
        if( $request->filled('area_filter') )
            $where['area_id'] = $request->area_filter;
        if( $request->filled('draw_time') )
            $where['draw_time'] = $request->draw_time;
    
        if( $user->role == 'area_admin' ){
            $where['area_id'] = $user->areas()->first()->id;
        }

        $tickets = Ticket::select('id', 'draw_date', 'draw_time', 'game_id', 'area_id', 'ticket_number')
            ->where($where)
            ->whereDate('draw_date', $date)
            ->get();

        $bets = new Collection();

        foreach ($tickets as $key => $ticket) {
            $grouped_bets = Bet::where('ticket_id', $ticket->id)
                ->get()
                ->groupBy('combination')
                ->map(function ($row) {
                    return array(
                        'amount_sum' => $row->sum('amount'),
                        'win_sum' => $row->sum('winning_amount'),
                        'type' => $row->pluck('type')->first(),
                        'id' => $row->pluck('id')->first(),
                    );
                });

            // check bets if passed the amount filter
            foreach( $grouped_bets as $combination => $details ){
                if( $details['amount_sum'] >= $amount_filter ){
                    $_ticket = Ticket::find($ticket->id);
                    $bets->push(array(
                        'combination'   => $combination,
                        'bet_id'        => $details['id'],
                        'bet_amount'    => $details['amount_sum'],
                        'bet_win'       => $details['win_sum'],
                        'bet_type'      => $details['type'],
                        'game'          => Game::find($ticket->game_id)->name,
                        'area'          => Area::find($ticket->area_id)->name,
                        'ticket_id'     => $ticket->id,
                        'draw_date'     => $ticket->draw_date,
                        'draw_time'     => $ticket->draw_time,
                        'ticket_number' => $ticket->ticket_number,
                        'transaction'   => $_ticket->transaction->code,
                        'user'          => $_ticket->transaction->user->firstname .' '. $_ticket->transaction->user->lastname,
                        // 'outlet'        => $_ticket->transaction->user->outlets()->first()
                    ));
                }
            }
        }

        return $bets;

    }
    
    public function getSalesPerAgent(Request $request)
    {
        $user = $request->user();
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $response_data = array();

        if ( $request->filled('coordinator') )
            $user = User::find($request->coordinator);
        
        if ($user->role == 'super_admin') {
            $agents = User::whereIn('role', ['usher','teller','coordinator'])->get();
            // ***TEMP - this serves as a display only
            $area = Area::all()->first();
        } elseif ($user->role == 'area_admin') {
            $area = $user->areas()->first();
            $agents = $area->areaAgents()->get();
        } elseif ($user->role == 'coordinator') {
            $agents = $user->children;
            $area = $user->areas()->first();
        } elseif ($user->role == 'usher' || $user->role == 'teller') {
            $agents = User::where('id', $user->id)->get();
            $area = $user->agentAreas()->first();
            $coordinator = $user->parent()->select('firstname', 'lastname')->where('role', 'coordinator')->first(); 
            $response_data['coordinator'] = $coordinator;   
        }

        $agents = $agents->merge(User::where('id', $user->id)->get())->sortBy('role');

        $sales = array();
        $total_sales_11am = 0;
        $total_sales_4pm = 0;
        $total_sales_9pm = 0;

        $total_hits_11am = 0;
        $total_hits_4pm = 0; 
        $total_hits_9pm = 0;

        $total_winning_amount_11am = 0;
        $total_winning_amount_4pm = 0;
        $total_winning_amount_9pm = 0;

        $total_tickets_11am = 0;
        $total_tickets_4pm = 0;
        $total_tickets_9pm = 0;

        foreach ($agents as $agent) {

            $_agent['id'] = $agent->id;
            $_agent['name'] = $agent->firstname . ' ' . $agent->lastname;

            // sales data
            $_11am = $this->getTransactionTotalPerTime($agent->transactions, $date, '11');
            $_4pm = $this->getTransactionTotalPerTime($agent->transactions, $date, '16');
            $_9pm = $this->getTransactionTotalPerTime($agent->transactions, $date, '21');

            $_agent['sales'] = array(
                '11am' => $_11am['sales'],
                '4pm' => $_4pm['sales'],
                '9pm' => $_9pm['sales'],
            );
            $_agent['sales']['total'] = array_sum($_agent['sales']);

            $_agent['cancelled_ticket_count'] = array(
                '11am' => $_11am['cancelled_ticket_count'],
                '4pm' => $_4pm['cancelled_ticket_count'],
                '9pm' => $_9pm['cancelled_ticket_count'],
            );
            $_agent['cancelled_ticket_count']['total'] = array_sum($_agent['cancelled_ticket_count']);

            $_agent['ticket_count'] = array(
                '11am' => $_11am['ticket_count'],
                '4pm' => $_4pm['ticket_count'],
                '9pm' => $_9pm['ticket_count'],
            );
            $_agent['ticket_count']['total'] = array_sum($_agent['ticket_count']);

            // winnings data
            $winners = $this->getWinBetsUnderAgent($agent->id, $date, $date);
            $_agent['hits'] = array(
                '11am' => $this->getTotalHitsPerDrawTime($winners, 11),
                '4pm' => $this->getTotalHitsPerDrawTime($winners, 16),
                '9pm' => $this->getTotalHitsPerDrawTime($winners, 21),
                'total' => $winners->sum(function ($winner) {
                    return $winner->bet->amount;
                })
            );
            $_agent['winnings'] = array(
                '11am' => $this->getTotalWinningsPerDrawTime($winners, 11),
                '4pm' => $this->getTotalWinningsPerDrawTime($winners, 16),
                '9pm' => $this->getTotalWinningsPerDrawTime($winners, 21),
                'total' => $winners->sum(function ($winner) {
                    return $winner->bet->winning_amount;
                })
            );

            $total_winners = $winners->groupBy('ticket_id');
            $_agent['winners_count'] = array(
                '11am' => $this->getTotalWinnersCountPerDrawTime($total_winners, '11'),
                '4pm' => $this->getTotalWinnersCountPerDrawTime($total_winners, '16'),
                '9pm' => $this->getTotalWinnersCountPerDrawTime($total_winners, '21'),
                'total' => $total_winners->count()
            );

            array_push($sales, $_agent);

            $total_sales_11am += $_agent['sales']['11am'];
            $total_sales_4pm += $_agent['sales']['4pm'];
            $total_sales_9pm += $_agent['sales']['9pm'];

            $total_hits_11am += $_agent['hits']['11am'];
            $total_hits_4pm += $_agent['hits']['4pm'];
            $total_hits_9pm += $_agent['hits']['9pm'];

            $total_winning_amount_11am += $_agent['winnings']['11am'];
            $total_winning_amount_4pm += $_agent['winnings']['4pm'];
            $total_winning_amount_9pm += $_agent['winnings']['9pm'];

            $total_tickets_11am += $_agent['ticket_count']['11am'];
            $total_tickets_4pm += $_agent['ticket_count']['4pm'];
            $total_tickets_9pm += $_agent['ticket_count']['9pm'];

        }

        // get winning amount per game ***TEMP
        $area_price = Price::select('price_per_unit')
        ->where([
            'area_id' => $area->id,
            'game_id' => 1
        ])->first();

        $response_data['agents'] = $sales;   
        $response_data['totals'] = array(
            'sales' => array(
                '11am' => $total_sales_11am,
                '4pm' => $total_sales_4pm,
                '9pm' => $total_sales_9pm
            ),
            // 'hits' => array(
            //     '11am' => $total_hits_11am,
            //     '4pm' => $total_hits_4pm,
            //     '9pm' => $total_hits_9pm
            // ),
            'hits' => array(
                '11am' => $total_winning_amount_11am / $area_price->price_per_unit,
                '4pm' => $total_winning_amount_4pm / $area_price->price_per_unit,
                '9pm' => $total_winning_amount_9pm / $area_price->price_per_unit,
            ),
            'winning_amount' => array(
                '11am' => $total_winning_amount_11am,
                '4pm' => $total_winning_amount_4pm,
                '9pm' => $total_winning_amount_9pm
            ),
            'tickets' => array(
                '11am' => $total_tickets_11am,
                '4pm' => $total_tickets_4pm,
                '9pm' => $total_tickets_9pm
            ),
        );
        $response_data['area'] = $area;   
        $response_data['user'] = User::select('role', 'firstname', 'lastname', 'email')->where('id', $user->id)->first();

        return $response_data;
    }

    public function getSalesPerAgent2(Request $request){

        $user = $request->user();
        $date = Carbon::parse($request->input('date', date('m/d/Y')));
        $date_to = $request->filled('date_to') ? Carbon::parse($request->input('date_to', date('m/d/Y'))) : $date;

        if ($request->filled('coordinator')){
            $user = User::find($request->coordinator);
        }
            
        $agents = $user->children->merge( User::whereId($user->id)->get() )->sortBy('role');

        $details = $agents->map(function ($user) use ($date, $date_to) {
            $subset = array();
            $salesHelper = new SalesHelper;
            $subset['info'] = collect($user->toArray())
                ->only(['id', 'firstname', 'lastname'])
                ->all();

            $subset['sales'] = array(
                '11am' =>  $salesHelper->getGrossSales($date, $date_to, 11, $user, true),
                '4pm' =>  $salesHelper->getGrossSales($date, $date_to, 16, $user, true),
                '9pm' =>  $salesHelper->getGrossSales($date, $date_to, 21, $user, true),
            );
            $subset['winnings'] = array(
                '11am' =>  $salesHelper->getWinnings($date, $date_to, 11, $user, true),
                '4pm' =>  $salesHelper->getWinnings($date, $date_to, 16, $user, true),
                '9pm' =>  $salesHelper->getWinnings($date, $date_to, 21, $user, true),
            );

            $winners = $this->getWinBetsUnderAgent($user->id, $date, $date_to);
            $total_winners = $winners->groupBy('ticket_id');
            $subset['winners_count'] = array(
                '11am' => $this->getTotalWinnersCountPerDrawTime($total_winners, '11'),
                '4pm' => $this->getTotalWinnersCountPerDrawTime($total_winners, '16'),
                '9pm' => $this->getTotalWinnersCountPerDrawTime($total_winners, '21'),
                'total' => $total_winners->count()
            );

            return $subset;
        });

        return array(
            'agents' => $details,
            'user' => collect($user->toArray())
                ->only(['id', 'email', 'firstname', 'lastname', 'percentage', 'float'])
                ->all(),
            'area' => collect($user->areas->first()->toArray())
                ->only(['id', 'name'])
                ->all()
        );

    }

    public function getCoordinatorsDetailed(Request $request){

        $coor = User::find($request->coor);
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $date_to = $request->filled('date_to') ? Carbon::parse($request->date_to) : Carbon::now();
        $salesHelper = new SalesHelper;

        $subset['gross_sales'] = array(
            '11am' =>  $salesHelper->getGrossSales($date, $date_to, 11, $coor)['gross_sales'],
            '4pm' =>  $salesHelper->getGrossSales($date, $date_to, 16, $coor)['gross_sales'],
            '9pm' =>  $salesHelper->getGrossSales($date, $date_to, 21, $coor)['gross_sales'],
        );
        
        $subset['winnings'] = array(
            '11am' =>  $salesHelper->getWinnings($date, $date_to, 11, $coor)['winning_amount'],
            '4pm' =>  $salesHelper->getWinnings($date, $date_to, 16, $coor)['winning_amount'],
            '9pm' =>  $salesHelper->getWinnings($date, $date_to, 21, $coor)['winning_amount'],
        );
        
        $subset['hits'] = array(
            '11am' =>  $salesHelper->getWinnings($date, $date_to, 11, $coor)['hits'],
            '4pm' =>  $salesHelper->getWinnings($date, $date_to, 16, $coor)['hits'],
            '9pm' =>  $salesHelper->getWinnings($date, $date_to, 21, $coor)['hits'],
        );

        return $subset;
    }

    public function getBetLimits(Request $request){
        if( $request->filled('area') ){
            return Betlimit::where('area_id', $request->area)->get();
        }else{
            return Betlimit::all();
        }
    }

    // ============================================================================
    // HELPER FUNCTIONS - should be separated on optimization phase 
    // ============================================================================

    public function getTransactionTotalPerTime($transactions, $date, $draw_time)
    {
        $sales = 0;
        $ticket_count = 0;
        $cancelled_ticket_count = 0;
        foreach ( $transactions as $transaction ) {
            $ticket_count += $transaction
                ->tickets()
                ->where('is_cancelled', false)
                ->where('draw_time', $draw_time)
                ->whereDate('draw_date', $date)
                ->count();
            
            $cancelled_ticket_count += $transaction
                ->tickets()
                ->where('is_cancelled', true)
                ->where('draw_time', $draw_time)
                ->whereDate('draw_date', $date)
                ->count();

            $sales += $transaction
                ->tickets()
                ->where('is_cancelled', false)
                ->where('draw_time', $draw_time)
                ->whereDate('draw_date', $date)
                ->sum('total_amount');
        }

        return array(
            'sales' => $sales,
            'ticket_count' => $ticket_count,
            'cancelled_ticket_count' => $cancelled_ticket_count,
        );
    }

    public function getWinBetsUnderAgent($agent_id, $date, $date_to)
    {
        $winners = Winner::where('user_id', $agent_id)
            // ->whereDate('created_at', $date)
            ->whereBetween('created_at', [$date, $date_to])
            ->with('bet')
            ->get();
        return $winners;
    }

    public function getTotalWinningsPerDrawTime($winners, $time)
    {
        return $winners->sum(function ($winner) use ($time) {
            if ($winner->bet->ticket->draw_time == $time)
                return $winner->bet->winning_amount;
            return 0;
        });
    }

    public function getTotalHitsPerDrawTime($winners, $time)
    {
        return $winners->sum(function ($winner) use ($time) {
            if ($winner->bet->ticket->draw_time == $time)
                return $winner->bet->amount;
            return 0;
        });
    }

    public function getTotalWinnersCountPerDrawTime($winners, $time)
    {
        $count = 0;
        foreach($winners as $key => $winner){
            if( Ticket::find( $key )->draw_time == $time )
             $count++;
        }
        return $count;
    }

    public function getRequestorArea($user)
    {
        if ($user->role == 'super_admin') {
            // ***TEMP - this serves as a display only
            $area = Area::all()->first();
        } elseif ($user->role == 'area_admin') {
            $area = $user->areas()->first();
        } elseif ($user->role == 'coordinator') {
            $area = $user->areas()->first();
        } elseif ($user->role == 'usher' || $user->role == 'teller') {
            $area = $user->agentAreas()->first();
        }
        return $area;
    }
    
}
