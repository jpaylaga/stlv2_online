<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayoutsController extends Controller
{
    public function get(Request $request)
    {
        return [];
    }

    public function addPayout(Request $request)
    {
        $ticket_number = $request->ticket_number;

        // check ticket in winners table


        return [];
    }
}
