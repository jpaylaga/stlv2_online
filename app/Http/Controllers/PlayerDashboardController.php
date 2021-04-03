<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlayerDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('players.dashboard');
    }

    public function winnings()
    {
        return view('players.winnings');
    }
}
