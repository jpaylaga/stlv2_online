<?php

namespace App\Helpers;

class GameValidatorHelper
{

    /**
     * Check validity of SWERTRES combination
     *
     * @param $bet
     * @return boolean
     */
    public function isValidSwertresCombination($bet)
    {
        if ( is_numeric($bet) && strlen($bet) === 3 ) {
            return true;
        }
        return false;
    }

    /**
     * Check validity of PARES bet
     *
     * @param $bet
     * @return boolean
     */
    public function isValidParesBet($bet)
    {
        if (is_numeric($bet) && strlen($bet) === 3) {
            return true;
        }
        return false;
    }
}

