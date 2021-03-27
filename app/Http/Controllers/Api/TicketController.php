<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Ticket;
use App\Game;
use App\Draw;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use GameHelper;
use App\Winner;
use App\Bet;
use App\Cancellation;
use App\User;
use App\Transaction;
use App\CreditHistory;

class TicketController extends Controller
{

    public function get(Request $request)
    {
        return Ticket::all();
    }

    public function cancelTicket(Request $request)
    {
        $user = $request->user();
        $can_cancel = false;
        $reason = $request->filled('reason') ? $request->reason : '';
        $message = '';
        $ticket = Ticket::where([
            'is_cancelled' => false,
            'is_checked' => false,
            'ticket_number' => $request->ticket_number
        ])->first();

        if( $ticket ){
            $now = Carbon::now();
        }else{
            return array('success' => false, 'message' => 'Unable to cancel ticket.');
        }

        if( $user->role === 'super_admin' ){
            $can_cancel = true;
        }elseif( $user->role === 'area_admin' ){
            $area = $user->areas()->first();
            if( $area->id == $ticket->area_id )
                $can_cancel = true;
        }elseif( in_array($user->role, array('usher', 'teller', 'coordinator')) && $now->diffInMinutes($ticket->created_at) < 5 ){
            $can_cancel = true;
        }else{
            $message = 'Ticket can not be cancelled. Please contact Admin.';
        }

        if( $can_cancel ){
            $ticket->is_cancelled = true;
            if( $ticket->save() ){
                // save to cancelled table
                $cancelled = new Cancellation();
                $cancelled->ticket_id = $ticket->id;
                $cancelled->user_id = $user->id;
                $cancelled->reason = $reason;
                $cancelled->save();

                // refund credits
                if( $ticket->is_credit ){
                    $owner = $ticket->transaction->user;
                    $owner->credits += $ticket->total_amount;
                    if( $owner->save() ){
                        CreditHistory::create([
                            'type' => 'refund',
                            'from' => $user->id,
                            'to' => $owner->id,
                            'amount' => $ticket->total_amount,
                            'user_id' => $user->id,
                        ]);
                    }
                }

                return array('success' => true, 'message' => $message);
            }
        }else{
            return array('success' => false, 'message' => $message);
        }

    }

    public function getCancelledTickets(Request $request)
    {
        $user = $request->user(); 
        $draw_date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $where = array('is_cancelled' => true);

        if( $user->role == 'super_admin' ){
            // return $this->_cancelledTickets($draw_date, $where);
        }elseif($user->role == 'area_admin'){
            $area = $user->areas()->first();
            $where['area_id'] = $area->id;
            // return $this->_cancelledTickets($draw_date, $where);
        }elseif($user->role == 'coordinator'){
            
        }

        $_tickets_query = Ticket::select('id', 'ticket_number', 'draw_date', 'draw_time', 'transaction_id', 'total_amount', 'area_id')
            ->where($where)
            ->whereDate('draw_date', $draw_date);

        if( $request->filled('ticket_number') ){
            $ticket_number = $request->ticket_number;
            $_tickets_query->where('ticket_number', 'LIKE', '%' . $ticket_number . '%');
        }

        $_tickets = $_tickets_query->get();

        $tickets = new Collection();
        foreach ($_tickets as $ticket) {
            $txn = Transaction::find($ticket->transaction_id); //separated so that transaction object will not be included in return
            $cn_details = Cancellation::where('ticket_id', $ticket->id)->first();

            $ticket->bets = Bet::where('ticket_id', $ticket->id)
                ->pluck('id', 'combination');

            $ticket->agent = User::select('firstname', 'lastname')
                ->where('id', $txn->user_id)
                ->with('outlets.name')
                ->first();

            $ticket->cancelled_by = User::select('firstname', 'lastname')
                ->where('id', $cn_details->user_id)->first();
            $ticket->details = $cn_details;

            $tickets->push($ticket);
        }
        
        return $tickets;

        // return [];

    }

    public function _cancelledTickets($draw_date, $where)
    {
        $_tickets_query = Ticket::select('id','ticket_number', 'draw_date', 'draw_time', 'transaction_id', 'total_amount', 'area_id')
            ->where($where)
            ->whereDate('draw_date', $draw_date);

        if( array_key_exists('ticket_number', $where) ){
            $ticket_number = $where['ticket_number'];
            $_tickets_query->where('ticket_number', 'LIKE', '%' . $ticket_number . '%');
        }

        $_tickets = $_tickets_query->get();

        $tickets = new Collection();
        foreach ($_tickets as $ticket) {
            $txn = Transaction::find($ticket->transaction_id); //separated so that transaction object will not be included in return
            $cn_details = Cancellation::where('ticket_id', $ticket->id)->first();
            
            $ticket->bets = Bet::where('ticket_id', $ticket->id)
                ->pluck('id', 'combination');

            $ticket->agent = User::select('firstname', 'lastname')
                ->where('id', $txn->user_id)
                ->with('outlets.name')
                ->first();
            
            $ticket->cancelled_by = User::select('firstname', 'lastname')
                ->where('id', $cn_details->user_id)->first();
            $ticket->details = $cn_details;

            $tickets->push($ticket);
        }
        return $tickets;
    }

    public function getTicketsToCheck(Request $request)
    {

        if( $request->filled('game_id') )
            $game = Game::find($request->game_id);
        
        return $game->tickets()->where([
            'draw_date' => Carbon::parse($request->draw_date),
            'draw_time' => $request->draw_time,
            'is_cancelled' => false,
        ])->get();

    }

    public function getTicket($ticket_id)
    {
        $ticket = Ticket::where('id', $ticket_id)->with('transaction', 'bets', 'game')->first();
        if( $ticket->count() > 0 )
            return $ticket;
        return [];
    }

    public function checkTicketsByDraw(Draw $draw)
    {

        $game = Game::find($draw->game_id);
        $winners = new Collection();

        $tickets = Ticket::where([
            'draw_date' => $draw->draw_date,
            'draw_time' => $draw->draw_time,
            'game_id' => $draw->game_id,
            'is_cancelled' => false,
            'is_checked' => false,
        ])->get();

        foreach ($tickets as $ticket) {
            foreach ($ticket->bets as $bet) {
                if( $bet->amount == 0 )
                    continue;
                    
                if (GameHelper::isWinningBet($bet->combination, $draw->result, $game->type, $bet->type)) {
                    //add to winners table
                    Winner::create([
                        'ticket_number' => $ticket->ticket_number,
                        'ticket_id' => $ticket->id,
                        'bet_id' => $bet->id,
                        'draw_id' => $draw->id,
                        'user_id' => $ticket->transaction->user->id,
                    ]);
                    $winners->push( $bet );
                } else {

                }
            }
            //update ticket checked status
            $ticket->is_checked = true;
            $ticket->save();
        }
        return $winners;

        // $bets = DB::table('tickets')
        //     ->join('bets', 'tickets.id', '=', 'bets.ticket_id')
        //     ->select('bets.*')
        //     ->where([
        //         'tickets.draw_date' => $draw->draw_date,
        //         'tickets.draw_time' => $draw->draw_time,
        //         'tickets.game_id' => $draw->game_id,
        //         'tickets.is_cancelled' => false,
        //         'tickets.is_checked' => false,
        //     ])->get();

    }

}
