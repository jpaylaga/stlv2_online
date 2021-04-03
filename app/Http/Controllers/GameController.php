<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('games');
    }

    public function listGames(){ return view('games.list'); }
    public function playStl(){ return view('games.stl'); }

}
