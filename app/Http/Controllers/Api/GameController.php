<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Game;
use App\Draw;
use App\Ticket;
use App\Winner;
use App\Helpers\BetHelper;

class GameController extends Controller
{

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        return Game::where('is_enabled', true)->get();
    }

    /**
     * Get a listing of the games with draw of specific date and time.
     *
     * @return \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getGamesWithDraw(Request $request)
    {

        $draw_date = Carbon::parse( $request->draw_date );
        $draw_time = $request->draw_time;

        $games = Game::where('is_enabled', true)->get();

        foreach ($games as $game) {

            $draw = Draw::where([
                'draw_date' => $draw_date,
                'draw_time' => $draw_time,
                'game_id' => $game->id
            ])->first();
            
            if( $draw ){
                $game->draw_id = $draw->id;
                $game->result = $draw->result;
                $game->winners = $draw->winners()->whereValid(true)->with('bet')->get();
                $game->hits = $draw->winners()
                    ->whereValid(true)
                    ->get()
                    ->sum(function($winner){
                        if ($winner->bet->type == 'ramble') {
                            $combinations_array = BetHelper::getCombinationsOfRamble($winner->bet->combination);
                            return $winner->bet->amount / count($combinations_array);
                        }
                        return (int) $winner->bet->amount;
                    });
                $game->total_tickets = Ticket::where([
                    'draw_date' => $draw_date,
                    'draw_time' => $draw_time,
                    'game_id' => $game->id,
                    'is_cancelled' => false,
                ])->count();
                $game->total_tickets_checked = Ticket::where([
                    'draw_date' => $draw_date,
                    'draw_time' => $draw_time,
                    'game_id' => $game->id,
                    'is_checked' => true,
                ])->count();
            }
            
        }

        return $games;  

    }

}
