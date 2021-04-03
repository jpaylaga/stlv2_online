<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Winner;
use App\Draw;
use App\Helpers\BetHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\User;
use App\CreditHistory;

class WinnerController extends Controller
{

    public function getSummary(Request $request)
    {

        $date = $request->filled('draw_date') ? Carbon::parse( $request->draw_date ) : Carbon::now();
        $user = $request->user();

        if( $user->role === 'coordinator' ) {
            $agents = $user->children;
        }elseif( $user->role === 'area_admin' ) {
            $area = $user->areas()->first();
            $agents = $area->areaAgents()->get();
        }elseif( $user->role === 'super_admin' ){
            $hits = Winner::whereDate('created_at', $date)
                ->whereValid(true)
                ->get()
                ->sum(function($winner){
                    if ($winner->bet->type == 'ramble') {
                        $combinations_array = BetHelper::getCombinationsOfRamble($winner->bet->combination);
                        return $winner->bet->amount / count($combinations_array);
                    }
                    return (int) $winner->bet->amount;
                });
            $total = Winner::whereDate('created_at', $date)
                ->whereValid(true)
                ->with('bet')->get()->sum(function($winner){
                    return $winner->bet->winning_amount;
                });
            return [
                'hits' => is_float($hits) ? number_format((float)$hits, 1, '.', '') : $hits,
                'total' => $total
            ];
        }else{
            return [];
        }

        $winnersCollection = new Collection();
        $winners = $this->collectAllWinnersUnderAgents( $agents, $winnersCollection, $date );
        $hits = $winners->sum(function($winner){
            if( $winner->bet->type == 'ramble' ){
                $combinations_array = BetHelper::getCombinationsOfRamble($winner->bet->combination);
                return $winner->bet->amount / count($combinations_array);
            }
            return $winner->bet->amount;
        });
        return [
            'hits' => is_float($hits) ? number_format((float)$hits, 1, '.', '') : $hits,
            'total' => $winners->sum(function($winner){
                return $winner->bet->winning_amount;
            })
        ];

    }

    public function collectAllWinnersUnderAgents($agents, $winners, $date){
        foreach ($agents as $agent) {
            $winners = $winners->merge(
                Winner::where('user_id', $agent->id)
                ->whereDate('created_at', $date)
                ->whereValid(true)
                ->with('bet')
                ->get()
            );
        }
        return $winners;
    }

    public function getWinners(Request $request)
    {
        // $whereDrawDate = $request->filled('draw_date') ? Carbon::parse( $request->draw_date ) : Carbon::now();
        $whereDrawDate = $request->filled('draw_date') ? Carbon::parse( $request->draw_date ) : false;

        $whereWinner = array();
        $whereDraw = array();

        if( $request->filled('draw_time') )
            $whereDraw['draw_time'] = $request->draw_time;

        if ($request->filled('status'))
            $whereWinner['status'] = $request->status;

        if ($request->filled('ticket_number'))
            $whereWinner['ticket_number'] = $request->ticket_number;

        // if ($request->filled('payout_date')){
        //     $whereWinner['payout_date'] = Carbon::parse($request->payout_date)->toDateTimeString();
        //     $whereDrawDate = true;
        // }

        if( $request->user()->role === 'coordinator' ) {
            $agents = $request->user()->children;
            $agents = $agents->merge(User::whereId($request->user()->id)->get());
            // $agents = User::whereId($request->user()->id)->get();
        }elseif( $request->user()->role == 'usher' || $request->user()->role == 'teller' || $request->user()->role == 'player' ) {
            $agents = User::where('id', $request->user()->id)->get();
        } elseif ($request->user()->role === 'area_admin') {
            $area = $request->user()->areas()->first();
            $agents = $area->areaAgents()->get();
        }elseif( $request->user()->role === 'super_admin' ){

            $draws = Draw::whereDate('draw_date', $whereDrawDate)->where($whereDraw)->get();
            $winners = new Collection();
            foreach ($draws as $draw) {
                $winners = $winners->merge(
                    Winner::where($whereWinner)
                    ->where('draw_id', $draw->id)
                    ->whereValid(true)
                    ->with('bet', 'draw.game', 'ticket.transaction.user.outlets')
                    ->get()
                );
            }
            return $winners;

        }

        $draws_query = Draw::where($whereDraw);

        if( $whereDrawDate ){
            $draws_query->whereDate('draw_date',$whereDrawDate);
        }

        $winners = new Collection();
        foreach ($agents as $agent) {
            foreach ($draws_query->get() as $draw) {
                $winners = $winners->merge(
                    Winner::where($whereWinner)
                    ->whereValid(true)
                    ->where([
                        'draw_id' => $draw->id,
                        'user_id' => $agent->id,
                    ])
                    ->with('bet', 'draw.game', 'ticket.transaction.user.outlets')
                    ->get()
                );
            }
        }
        return $winners;

    }

    public function getWinner(Request $request)
    {
        $user = $request->user();
        $can_payout = false;

        $winning_tickets = Winner::where([
            'status' => 'pending',
            'valid' => true,
            'ticket_number' => $request->ticket_number
        ])->get();

        if($winning_tickets->count() > 0 ){
            $wt_ref = $winning_tickets->first();
            $ticket_issuer = User::find($wt_ref->user_id);

            // check if user id authorize
            switch ($user->role) {
                case 'super_admin':
                    $can_payout = true;
                    break;
                case 'area_admin':
                    $admin_area = $user->areas()->first();
                    $area = $ticket_issuer->agentAreas()->first();
                    if ($admin_area->id == $area->id)
                        $can_payout = true;
                    break;
                case 'coordinator':
                    if ($user->children->contains($ticket_issuer) || $user->id == $ticket_issuer->id)
                        $can_payout = true;
                    break;
                case 'usher':
                case 'teller':
                    if ($user->id == $ticket_issuer->id)
                        $can_payout = true;
                    break;
                default:
                    break;
            }

            unset( $winning_tickets->user );
            if( $can_payout )
                return array( 'success' => true, 'tickets' => $winning_tickets->load('bet') );
            
            return array( 'success' => false, 'message' => 'Unauthorize Payout.' );
            
        }

        return array( 'success' => false, 'message' => 'Ticket not found' );

    }

    public function payWinner(Request $request)
    {
        if( !$request->filled('ticket_number') )
            return array('success' => false, 'message' => 'Ticket not found');

        $win = Winner::whereTicket_number($request->ticket_number)
            ->whereValid(true)
            ->where('status', 'pending')
            ->first();

        if($win){
            $win_amount = $win->bet->winning_amount;
            if( in_array( auth()->user()->role, ['coordinator', 'area_admin']) ){
                if( (int)auth()->user()->creditBalance() < (int)$win_amount )
                    return ['success' => false, 'message' => 'You have insufficient credits to transfer to this account.'];
                // transfer amount from coordinator to player
                CreditHistory::create([
                    'type' => 'transfer',
                    'to' => $win->user_id,
                    'from' => auth()->user()->id,
                    'amount' => $win_amount,
                    'user_id' => auth()->user()->id,
                    'description' => 'Winnings.'
                ]);
            }
            $win->status = 'paid';
            $win->payout_date = Carbon::now();
            $win->user_paid = $request->user()->id;
            $win->save();
        }else{
            return array('success' => false, 'message' => 'Ticket not found or invalid.');
        }
        // foreach($wins as $win){
        //     $win->status = 'paid';
        //     $win->payout_date = Carbon::now();
        //     $win->user_paid = $request->user()->id;
        //     $win->save();
        // }

        return array('success' => true);

    }

    public function deleteDuplicate(Request $request)
    {
        if( !$request->filled('winner_id') )
            return array('success' => false, 'message' => 'Ticket not found');

        $duplicate = Winner::find($request->winner_id);
        $duplicate->valid = false;
        $duplicate->save();

        return array('success' => true);

    }

}
