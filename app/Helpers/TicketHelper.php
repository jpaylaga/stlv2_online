<?php

namespace App\Helpers;

use App\Bet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\User;

class TicketHelper
{
    public static function getTickets($date_from, $date_to, $draw_time='', $user, $exclusive=false, $with_bets=false, $game='', $ticket_number='', $agents=null)
    {   
        $area = null;

        if ($user->role == 'super_admin') {
          
        } elseif ( $user->role == 'area_admin' ) {
            $area = $user->areas()->first();
        } elseif ( $user->role == 'coordinator' ) {
            if( $exclusive ){
                $agents = User::where('id', $user->id)->get();
            }else{
                $agents = $user->children;
                $agents->push($user);
            }
            $agents = $agents->pluck('id');
        } elseif ( in_array($user->role, ['usher', 'teller', 'player']) ) {
            $agents = User::where('id', $user->id)->get();
            $agents = $agents->pluck('id');
        }

        $transactions_query = DB::table('transactions')
            ->join('tickets', 'transactions.id', '=', 'tickets.transaction_id')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select('tickets.id', 'tickets.created_at', 'tickets.draw_date', 'tickets.draw_time', 'tickets.is_cancelled', 'tickets.ticket_number', 'tickets.total_amount', 'transactions.code as txn_code', 'transactions.id as txn_id', 'users.firstname', 'users.lastname')
            ->whereBetween('tickets.draw_date', [$date_from, $date_to])
            ->where('tickets.total_amount', '>',  0);
            // ->where('tickets.is_cancelled', false);
        if( $user->role != 'player' ){
            $transactions_query->where('tickets.is_cancelled', false);
        }

        // FILTER
        if( $agents )
            $transactions_query->whereIn('transactions.user_id', $agents);
        if( $area )
            $transactions_query->where('tickets.area_id', $area->id);
        if( $draw_time )
            $transactions_query->where('tickets.draw_time', $draw_time);
        if ( $game )
            $transactions_query->where('tickets.game_id', $game);
        if ( $ticket_number )
            $transactions_query->where('tickets.ticket_number', $ticket_number);

        $tickets = $transactions_query->get();

        if( $with_bets ){
            $tickets->map(function ($ticket) {
                $ticket->bets = Bet::where('ticket_id', $ticket->id)->pluck('id', 'combination');
                return $ticket;
            });
        }

        return $tickets;

    } //@end getTickets

    /**
     * Get ticket total sales
     * 
     * @return Ticket $tickets
     */
    public static function getTicketsTotalSalesByTime($tickets, $time)
    {   
        return $tickets->where('draw_time', $time)->sum('total_amount');
    }

    public static function getTicketSalesUnderCoordinatorPerTime($agents, $date, $time = 0)
    {
        $transactions_query = DB::table('transactions')
            ->join('tickets', 'transactions.id', '=', 'tickets.transaction_id')
            ->select('tickets.id', 'tickets.total_amount')
            ->whereDate('tickets.draw_date', $date)
            ->where('tickets.total_amount', '>',  0)
            ->whereIn('transactions.user_id', $agents)
            ->where('tickets.is_cancelled', false);
        
        if( $time > 0 )
            $transactions_query->where('tickets.draw_time', $time);

        return $transactions_query->sum('tickets.total_amount');

    }
  
}
