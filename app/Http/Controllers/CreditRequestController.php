<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('credit.requests');
    }

    public function references()
    {
        return view('credit.references');
    }
}
