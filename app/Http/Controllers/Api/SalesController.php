<?php

namespace App\Http\Controllers\Api;

use App\Draw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Ticket;
use Illuminate\Database\Eloquent\Collection;
use App\User;
use App\Game;
use App\Transaction;
use App\Winner;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use App\Helpers\BetHelper;
use App\Helpers\SalesHelper;

class SalesController extends Controller
{
    /**
     * Retrieve draw sales
     * params:
     *  - date_from
     *  - area
     *  - 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDrawSales(Request $request)
    {   

        $user = $request->user();

        // check draw_date and return false
        if( $request->filled('draw_date') ){
            $draw_date = Carbon::parse($request->draw_date);
        }else{
            return response('Draw Date and Time is required', 422); 
        }

        // define default args
        $args = array(
            'draw_date' => $draw_date,
        );

        if( $user->role === 'area_admin' ){
            $area = $user->areas()->first();
            // check if has area, return false
            if( !$area )
                return response('User has no area linked to.', 422); 
            $args['area_id'] = $area->id; 
        }elseif( $user->role == 'coordinator' || $user->role == 'usher' || $user->role == 'teller' ){

            if( $user->role == 'coordinator' ){
                $agents = $user->children;
                $agents = $agents->merge(User::where('id', $user->id)->get())->sortBy('role');
            }else{
                $agents = User::where('id', $user->id)->get();
            }

            $tickets = new Collection();
            foreach ( $agents as $agent ) {
                foreach ($agent->transactions as $transaction) {
                    $tickets = $tickets->merge( 
                        $transaction->tickets()->where($args)->where('is_cancelled', false)->get() 
                    );
                }
            }
            return $tickets;

        }

        $tickets = Ticket::select('id','total_amount')
            ->where($args)
            ->where('is_cancelled', false)->get();
        
        return $tickets;
    }

    public function getSalesPerGame(Request $request)
    {
        $user = $request->user();
        $draw_date = Carbon::parse($request->draw_date);
        $agents = []; 
        $games = Game::where('is_enabled', true)->get();

        if ($user->role === 'coordinator') {
            $agents = $user->children;
        } elseif ($user->role == 'usher' || $user->role == 'teller') {
            $agents = User::where('id', $user->id)->get();
        } elseif ($user->role === 'area_admin') {
            $area = $user->areas()->first();
            $agents = $area->areaAgents()->get();
            $agents = $agents->merge(User::whereRole('coordinator')->get());
        }

        foreach ($games as $game) {
            $game->total_sales_11am = $this->getDrawTimeSales('11', $game->id, $draw_date, $agents, $user->role);
            $game->total_sales_4pm = $this->getDrawTimeSales('16', $game->id, $draw_date, $agents, $user->role);
            $game->total_sales_9pm = $this->getDrawTimeSales('21', $game->id, $draw_date, $agents, $user->role);
        }
        return $games;
    }

    public function getSummarySales(Request $request)
    {
        $user = $request->user();
        $date_from = Carbon::parse( $request->input('date_from', date('m/d/Y') ) );
        $date_to = Carbon::parse($request->input('date_to', date('m/d/Y')));
        $draw_time = $request->input('draw_time', '');
        $percentage = $request->input('percentage', 0);
        $comm = $request->input('comm', 0);

        $period = CarbonPeriod::create($date_from, $date_to);
        $data = new Collection();
        $salesHelper = new SalesHelper;

        // Iterate over the period
        foreach ($period as $date) {

            // winnings
            $winnings_raw = $salesHelper->getWinnings($date, $date, $draw_time, $user);
            $winnings = $winnings_raw['winning_amount'];
            $hits = $winnings_raw['hits'];

            //gross_sales
            $gross_sales_raw = $salesHelper->getGrossSales($date, $date, $draw_time, $user);
            $gross_sales = $gross_sales_raw['gross_sales'];
            $net_sales = $gross_sales - ( $gross_sales * ($percentage / 100) );
            $winning_amount = $winnings + ($hits * $comm);

            $data->push(
                array(
                    'date' => $date->format('m/d/Y'),
                    'gross_sales' => $gross_sales,
                    'net_sales' => $net_sales,
                    'hits' => $hits,
                    'ticket_count' => $gross_sales_raw['ticket_count'],
                    'winning_amount' => $winning_amount,
                    'earnings' => (float) number_format(($net_sales - $winning_amount), 2, '.', '' ),
                )
            );

        }

        return $data;

    }

    public function getGrossSalesByDrawTime(Request $request){
        $user = $request->user();
        $date_from = Carbon::parse($request->input('date_from', date('m/d/Y')));
        $date_to = Carbon::parse($request->input('date_to', date('m/d/Y')));
        $salesHelper = new SalesHelper;

        $gross_sales = array();

        foreach ([11,16,21] as $draw_time) {
            $gs_raw = $salesHelper->getGrossSales($date_from, $date_to, $draw_time, $user);
            $gross_sales[$draw_time] = $gs_raw['gross_sales'];
        }

        return $gross_sales;

    }

    public function getWinningsByDrawTime(Request $request)
    {
        $user = $request->user();
        $date_from = Carbon::parse($request->input('date_from', date('m/d/Y')));
        $date_to = Carbon::parse($request->input('date_to', date('m/d/Y')));
        $salesHelper = new SalesHelper;

        $winnings = array();
        $hits  = array();

        foreach ([11, 16, 21] as $draw_time) {
            $winnings_raw = $salesHelper->getWinnings($date_from, $date_to, $draw_time, $user);
            $winnings[$draw_time] = $winnings_raw['winning_amount'];
            $hits[$draw_time] = $winnings_raw['hits'];
        }

        return array(
            'winnings' => $winnings,
            'hits' => $hits
        );
    }

    public function getDrawTimeSales($draw_time, $game_id, $draw_date, $agents, $role)
    {
        $sales = 0;

        if( $role == 'super_admin' ){
            $sales = Ticket::whereDate('draw_date', $draw_date)
                ->where('is_cancelled', false)
                ->where('draw_time', $draw_time)
                ->where('game_id', $game_id)
                ->sum('total_amount');
        }else{
            foreach ($agents as $agent) {
                foreach ($agent->transactions as $transaction) {
                    $sales += $transaction
                        ->tickets()
                        ->whereDate('draw_date', $draw_date)
                        ->where('is_cancelled', false)
                        ->where('draw_time', $draw_time)
                        ->where('game_id', $game_id)
                        ->sum('total_amount');
                }
            }
        }
        return $sales;
    }

    // A total summary report of sales in a specific date rang 
    public function getResultSales(Request $request){
        $user = $request->user();
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        $date_to = $request->filled('date_to') ? Carbon::parse($request->date_to) : Carbon::now();

        if( $user->role == 'super_admin' ){
            $coordinators_query = User::where('role', 'coordinator');
        }elseif( $user->role == 'area_admin' ){
            $area_coordinators = $user->areas()->first()->users->where('role', 'coordinator')->pluck('id');
            $coordinators_query = User::whereIn('id', $area_coordinators);
        }elseif( $user->role == 'coordinator' ){
            $coordinators_query = User::whereId($user->id);
        }else{
            return [];
        }

        $coordinators = $coordinators_query->get();
        $summary['gross_sales'] = array();
        $summary['winnings'] = array();
        $summary['commission'] = array();
        $summary['profit'] = array();
        $_draw_times = array( '11am' => 11, '4pm' => 16, '9pm' => 21 );
        foreach ($_draw_times as $key => $draw_time) {
            $gross_sales = 0;
            $winnings = 0;
            $commission = 0;
            $profit = 0;
            $float = 0;
            $hits = 0;
            foreach($coordinators as $coor){
                $salesHelper = new SalesHelper;
                $raw_winnings = $salesHelper->getWinnings($date, $date_to, $draw_time, $coor);

                $_gross = $salesHelper->getGrossSales($date, $date_to, $draw_time, $coor)['gross_sales'];
                $_winnings = $raw_winnings['winning_amount'];
                $_hits = $raw_winnings['hits'];
                $_float = $coor->float * $_hits;

                $commission += $_gross * ($coor->percentage / 100);
                $profit += $_gross * ( (100 - $coor->percentage) / 100) - ($_winnings + $_float);
                $gross_sales += $_gross;
                $winnings += ($_winnings + $_float);
                $float += $_float;
                $hits += $_hits;
            }
            $summary['gross_sales'][$key] = $gross_sales;
            $summary['winnings'][$key] = $winnings;
            $summary['commission'][$key] = $commission;
            $summary['float'][$key] = $float;
            $summary['hits'][$key] = $hits;
            $summary['profit'][$key] = $profit;
        }

        return $summary;

    }

}
