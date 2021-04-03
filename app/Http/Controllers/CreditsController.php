<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('credits');
    }

    public function info() { return view('credit.info'); }
    public function topup() { return view('credit.topup'); }
    public function withdraw() { return view('credit.withdraw'); }
}
