<?php 

namespace App\Helpers;

use App\Transaction;
use App\Ticket;

class GeneratorHelper
{
    /**
     * Generate 16-digit code.
     *
     * @return string
     */
    public static function generateUniqueCode()
    {
        $code = sprintf(
            "%s-%s-%s-%s",
            str_random(4),
            str_random(4),
            str_random(4),
            str_random(4)
        );
        return $code;
    }

    /**
     * Generate Transaction code.
     *
     * @return string
     */
    public static function generateTxnCode()
    {
        $code = self::generateUniqueCode();
        while (Transaction::where('code', $code)->count() > 0) {
            $code = self::generateUniqueCode();
        }
        return $code;
    }

    /**
     * Generate Ticket Number.
     *
     * @return string
     */
    public static function generateTicketNum()
    {
        $code = self::generateUniqueCode();
        while (Ticket::where('ticket_number', $code)->count() > 0) {
            $code = self::generateUniqueCode();
        }
        return $code;
    }

    /**
     * Generate Registration Approval Code
     *
     * @return string
     */
    public static function generateRegCode()
    {
        return strtoupper( sprintf( "%s", str_random(16) ) );
    }
}
