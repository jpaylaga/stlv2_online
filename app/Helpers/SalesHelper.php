<?php

namespace App\Helpers;

use App\Draw;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesHelper
{

    /**
     * Get total gross
     * 
     * @param date $date_from
     * @param date $date_to
     * @return array ($gross_sales, $ticket_count)
     */
    public function getGrossSales($date_from, $date_to, $draw_time = '', $user, $exclusive = false)
    {
        $area = null;
        $agents = null;

        if( $user->role == 'super_admin' ){

        }elseif( $user->role == 'area_admin' ){
            $area = $user->areas()->first();
        }elseif( $user->role == 'coordinator' ){
            if( $exclusive ){
                $agents = array($user->id);
            }else{
                $agents = $user->children;
                $agents = $agents->merge(User::whereId($user->id)->get())->sortBy('role');
                $agents = $agents->pluck('id');
            }
        }elseif( $user->role == 'usher' || $user->role == 'teller' ){
            $agents = array( $user->id );
        }

        $tickets_query = DB::table('transactions')
            ->join('tickets', 'transactions.id', '=', 'tickets.transaction_id')
            ->select('tickets.id', 'tickets.draw_date', 'tickets.total_amount')
            ->whereBetween('tickets.draw_date', [$date_from, $date_to])
            ->where('tickets.is_cancelled', false)
            ->where('tickets.total_amount', '>',  0);
        
        if( $area )
            $tickets_query->where('tickets.area_id', $area->id);
        if( $draw_time )
            $tickets_query->where('tickets.draw_time', $draw_time);
        if( $agents )
            $tickets_query->whereIn('transactions.user_id', $agents);

        $tickets = $tickets_query->get();

        return array(
            'gross_sales' => $tickets->sum('total_amount'),
            'ticket_count' => $tickets->count()
        );
    }

    /**
     * Get total winnings
     * 
     * @param date $date_from
     * @param date $date_to
     * @return array ($gross_sales, $ticket_count)
     */
    public function getWinnings($date_from, $date_to, $draw_time = '', $user, $exclusive = false)
    {
        $area = null;
        $agents = null;

        if ($user->role == 'super_admin') { 

        } elseif ($user->role == 'area_admin') {
            $area = $user->areas()->first();
        } elseif ($user->role == 'coordinator') {
            if ($exclusive) {
                $agents = array($user->id);
            } else {
                $agents = $user->children;
                $agents = $agents->merge(User::whereId($user->id)->get())->sortBy('role');
                $agents = $agents->pluck('id');
            }
        } elseif ($user->role == 'usher' || $user->role == 'teller') {
            $agents = array($user->id);
        }

        //winners
        $draws = Draw::whereBetween('draw_date', [$date_from, $date_to])->get();
        $winners_query = DB::table('bets')
            ->join('winners', 'bets.id', '=', 'winners.bet_id')
            ->join('tickets', 'tickets.id', '=', 'winners.ticket_id')
            ->select('bets.id', 'bets.type', 'bets.combination', 'bets.amount', 'tickets.draw_date', 'bets.winning_amount')
            ->whereBetween('tickets.draw_date', [$date_from, $date_to])
            ->where('winners.valid', true)
            ->whereIn('draw_id', $draws->pluck('id'));

        if ($area)
            $winners_query->where('tickets.area_id', $area->id);
        if ($draw_time)
            $winners_query->where('tickets.draw_time', $draw_time);
        if ($agents)
            $winners_query->whereIn('winners.user_id', $agents);

        $winners = $winners_query->get();

        $hits = $winners->sum(function ($winner) {
            if ($winner->type == 'ramble') {
                $combinations_array = BetHelper::getCombinationsOfRamble($winner->combination);
                return $winner->amount / count($combinations_array);
            }
            return (int) $winner->amount;
        });

        return array(
            'hits' => $hits,
            'winning_amount' => $winners->sum('winning_amount'),
            // 'float' => $total_float
        );
    }

}