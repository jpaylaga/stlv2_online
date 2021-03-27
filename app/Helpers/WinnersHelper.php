<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class WinnersHelper
{
    /**
     * Get winners total winning amount
     * 
     * @return sum $total_win
     */
    public static function getTotalWinningsPerDrawTime($winners, $time)
    {   
        return $winners->sum(function ($winner) use ($time) {
            if ($winner->bet->ticket->draw_time == $time)
                return $winner->bet->winning_amount;
            return 0;
        });
    }

    public static function getTotalHitsPerDrawTime($winners, $time)
    {   
        return $winners->sum(function ($winner) use ($time) {
            if ($winner->bet->ticket->draw_time == $time)
                return $winner->bet->amount;
            return 0;
        });
    }
  
}
