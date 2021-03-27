<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{

    public function summarySales()
    {
        return view('summary-sales');
    }

    public function summary()
    {
        return view('summary-reports');
    }

    public function highestBet()
    {
        return view('highest-bet');
    }

    public function hotNumbers()
    {
        return view('hot-numbers');
    }

    public function betLimits()
    {
        return view('bet-limits');
    }

    public function payoutRates()
    {
        return view('payout-rates');
    }

    public function reportsCoordinators()
    {
        return view('reports-coordinators');
    }

    public function areaSummary()
    {
        return view('area-summary');
    }
}
