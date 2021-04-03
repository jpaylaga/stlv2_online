<?php

namespace App\Helpers;

use App\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class BetHelper
{
    /**
     * Get winning amount of bet
     * 
     * @param array $bet
     * @param double $price_per_unit
     * @return int $winning_amount
     */
    public static function getWinningAmount($bet, $amount, $price_per_unit)
    {
        $combinations_list = self::getCombinationsOfRamble( $bet['combination'] );
        $price_per_unit = self::getPricePerUnit($combinations_list, $price_per_unit);
        $winning_amount = 0;

        if( $bet['type'] == 'straight' ){
            $winning_amount = $amount * $price_per_unit;
        }elseif( $bet['type'] == 'ramble' ){
            // multiplier = bet_amount / # of combinations
            // $combinations_list = self::getCombinationsOfRamble( $bet['combination'] );
            $multiplier = $amount / count( $combinations_list );
            $winning_amount = $multiplier * $price_per_unit;
        }
        return $winning_amount;
    }

    public static function getRambleTotalAmount($rambled_array, $where, $date)
    {
        $ramble_occurence = DB::table('tickets')
            ->join('bets', 'tickets.id', '=', 'bets.ticket_id')
            ->select('bets.id', 'bets.combination', 'bets.type', 'amount')
            ->whereDate('draw_date', $date)
            ->where($where)
            ->where(function ($query) use ($rambled_array) {
                $query->whereIn('combination', $rambled_array);
                $query->where('type', 'ramble');
            })->get();

        return $ramble_occurence->sum('amount');
    }

    public static function getTargetTotalAmount($combination, $where, $date)
    {
        $bets = DB::table('tickets')
            ->join('bets', 'tickets.id', '=', 'bets.ticket_id')
            ->select('bets.id', 'bets.combination', 'bets.type', 'amount')
            ->whereDate('draw_date', $date)
            ->where($where)
            ->where('bets.type', 'straight')
            ->where('bets.combination', $combination)->get();
        
        return $bets->sum('amount');
    }

    /**
     * Get combinations of rambled bet
     * 
     * Ref: https://gist.github.com/codyphobe/3d0db49b4024b3da96b51f6e8f5d14fc
     * 
     * @return array $combinations
     */
    public static function getCombinationsOfRamble( $combination )
    {
        Collection::macro('permute', function () {
            if ($this->isEmpty()) {
                return new static([[]]);
            }
            return $this->flatMap(function ($value, $index) {
                return ( new static($this) )
                    ->forget($index)
                    ->values()
                    ->permute()
                    ->map(function ($item) use ($value) {
                        return (new static($item))->prepend($value);
                    });
            });
        });

        $combinations = collect( str_split($combination) )->permute()->toArray();

        return array_unique( array_map(function( $comb ){
            return implode('', $comb);
        }, $combinations) );
    }

    public static function getPricePerUnit( $combinations_list, $price_per_unit ){
        if( count( $combinations_list ) == 1 ){ //triple
            $setting = Setting::where('slug', 'triple')->first();
            return $setting ? (float)$setting->val : $price_per_unit;
        }elseif( count( $combinations_list ) == 3 ){ //double
            $setting = Setting::where('slug', 'double')->first();
            return $setting ? (float)$setting->val : $price_per_unit;
        }else{
            return $price_per_unit;
        }
    }

    public static function getHotumbersControl( $user, $date_from, $date_to, $draw_time='', $combination, $type ){

        $area = null;

        if( $user->role == 'area_admin' ) {
            $area = $user->areas()->first();
        }

        $bets_query = DB::table('tickets')
            ->join('bets', 'tickets.id', '=', 'bets.ticket_id')
            ->select('bets.id', 'bets.ticket_id', 'bets.combination', 'bets.type', 'amount', 'tickets.draw_date')
            ->whereBetween('tickets.draw_date', [$date_from,$date_to])
            ->where('tickets.total_amount', '>', 0)
            ->where('tickets.is_cancelled', false);

        if( $area )
            $bets_query->where('tickets.area_id', $area->id);
        if( $draw_time )
            $bets_query->where('tickets.draw_time', $draw_time);

        // filter here
        if ( $combination ) {
            $rambled_array = self::getCombinationsOfRamble($combination);
            $bets_query->where('bets.combination', $combination);

            $bets_query->orWhere(function ($query) use ($rambled_array, $date_from, $date_to, $draw_time) {
                $query->whereIn('bets.combination', $rambled_array);
                $query->whereBetween('tickets.draw_date', [$date_from,$date_to]);
                $query->where('tickets.is_cancelled', false);
                $query->where('bets.type', 'ramble');
                if( $draw_time )
                    $query->where('tickets.draw_time', $draw_time);
            });
        }

        $combinations_raw = $bets_query->get()->groupby('combination');
        $combinations = new Collection;

        foreach ($combinations_raw as $combi => $group) {

            // get bet limit
            // $bet_limit = $area ? $area->limit : env('DEFAULT_BET_LIMIT', 200);
            // $_bet_limit = Betlimit::where('combination', $combination)->first();
            // if( $_bet_limit ){
            //     $bet_limit = $_bet_limit->limit;
            // }

            $types = $group->groupby('type')->map(function ($row, $type) use ($combi) {
                    if( $type == 'ramble' ){
                        $divisor = self::getCombinationsOfRamble($combi);
                        return $row->sum('amount') / count($divisor);
                    }
                    return $row->sum('amount');
                });
            
            $total_amount = 0;
            foreach ($types as $key => $value) {
                $total_amount += $value;
            }

            $combinations->push(
                array(
                    'combination' => $combi,
                    'bet_count' => count($group),
                    'total_amount' => $total_amount,
                    'types' => $types,
                )
            );
        }

        return $combinations;

    }
  
}
