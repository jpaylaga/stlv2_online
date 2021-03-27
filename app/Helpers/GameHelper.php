<?php

namespace App\Helpers;

use BetHelper;

class GameHelper
{

    // protected $gameValidator;

    public function __construct()
    {
        // $this->gameValidator = new GameValidatorHelper();
    }

    /**
     * Check combination if win
     *
     * @param string $combination
     * @param string $result
     * @param string $game_type
     * @param string $game_type
     * @return mixed
     */
    public static function isWinningBet($combination, $result, $game_type, $bet_type)
    {

        if( $game_type === 'swertres' ){
            
            return self::checkSwertres($combination, $result, $bet_type );

        }elseif( $game_type === 'pares' ){

            return self::checkPares($combination, $result);

        }

    }

    /**
     * SWERTRES: check if win
     *
     * @param App\Bet $bet
     * @return mixed
     */
    public static function checkSwertres($combination, $result, $bet_type)
    {
        if ( self::isValidSwertresCombination($combination) && self::isValidSwertresCombination($result) ) {
            if ($bet_type === 'straight' && ( $combination == $result ) ) {
                return true;
            } else if ($bet_type == 'ramble') {
                $combinations_array = BetHelper::getCombinationsOfRamble($combination);
                if( in_array($result, $combinations_array) ){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * PARES: check if win
     *
     * @param App\Bet $bet
     * @return mixed
     */
    public static function checkPares($bet, $draw)
    { 

    }

    /**
     * Check validity of SWERTRES combination
     *
     * @param string $bet
     * @return boolean
     */
    public static function isValidSwertresCombination($bet)
    {
        if (is_numeric($bet) && strlen($bet) === 3) {
            return true;
        }
        return false;
    }


}
