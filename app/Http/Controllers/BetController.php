<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;

class BetController extends Controller
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
     * Show the bet index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('super_admin.bets');
    }
}
