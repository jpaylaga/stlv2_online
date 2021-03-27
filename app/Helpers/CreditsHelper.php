<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreditsHelper
{
    /**
     * Get winning amount of bet
     * 
     * @param array $bet
     * @param double $price_per_unit
     * @return int $winning_amount
     */
    public static function getBetTotalPayable($raw_tickets)
    {
        $total = 0;
        foreach ($raw_tickets as $_ticket) {
            foreach ( $_ticket['bets'] as $_bet ) {
                $total += (int) $_bet['amount'];
            }
        }
        return $total;
    }
  
}
