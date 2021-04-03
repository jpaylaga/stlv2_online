<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Draw;
use Carbon\Carbon;
use App\Game;

class DrawController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'draw_date' => 'required|date',
            'draw_time' => 'required|integer',
        ]);

        if( !$request->filled('result') ){
           return [];
        }

        $draw = new Draw();
        $draw->draw_date = Carbon::parse($request->draw_date);
        $draw->draw_time = $request->draw_time;
        $draw->result = $request->result;
        $draw->game_id = $request->game_id;
        $draw->user_id = $request->user()->id;
        $draw->save();

        return $draw;

    }

    public function get(Request $request)
    {
        $date = $request->filled('draw_date') ? Carbon::parse($request->draw_date) : Carbon::now();
        $where = array('draw_date' => $date);

        if( $request->filled('draw_time') )
            $where['draw_time'] = $request->draw_time;

        $draws = Draw::where($where)
            ->with('game')
            ->get();

        return $draws;
    }

    public function getResultsByGame(Request $request)
    {
        $games = Game::where('is_enabled', true)->get();   
        foreach ($games as $game) {
            $results = Draw::where([
                'draw_date' => Carbon::parse($request->draw_date),
                'game_id' => $game->id,
            ])->get();
            $game->results = $results;
        }
        return $games;
    }

}
