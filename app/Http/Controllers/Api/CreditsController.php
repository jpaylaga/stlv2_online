<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use Carbon\Carbon;
use App\CreditHistory;
use App\CreditRequest;

class CreditsController extends Controller
{
    public function get(Request $request)
    {
        $role = $request->role;

        $users_query = DB::table('users')
            ->select('id','firstname','lastname','role','credits', 'activate_credit');

        if( $role )
            $users_query->whereRole($role);
        else
            $users_query->whereIn('role', ['player','teller','usher','coordinator']);

        $users = $users_query->get();

        $users->map(function ($user) {
            $temp = User::find($user->id);
            $user->credits = $temp->creditBalance();
            return $user;
        });

        return $users;
    }

    public function add(Request $request)
    {
        $to = User::find($request->user);
        $credits = $request->credits;
        $new_credits = $to->credits + $credits; 

        $creditrequest = CreditRequest::create([
            'type' => 'deposit',
            'status' => 'done',
            'amount' => $credits,
            'outlet' => 'manual',
            'player_id' => $to->id,
            'user_id' => $request->user()->id,
        ]);

        if( !$creditrequest->updateUserCredit() ){
            return ['success' => false, 'message' => 'Something went wrong on updating user credit, please try again.'];
        }

        return array('success'=>false);
    }

    public function transfer(Request $request)
    {
        $user = User::find($request->user);
        $amount = abs($request->amount);
        $from = User::find($request->from); 
        $to = User::find($request->to); 

        if( $from->credits >= $amount ){
            $from->credits -= $amount;
            $to->credits += $amount;

            if( $from->save() && $to->save() ){
                CreditHistory::create([
                    'type' => 'transfer',
                    'from' => $from->id,
                    'to' => $to->id,
                    'amount' => $amount,
                    'user_id' => $request->user()->id,
                ]);
                return array('success'=>true);
            }else{
                return array('success'=>false, 'message'=>'Something went wrong. Please try again.');
            }

        }else{
            return array('success'=>false, 'message'=>'Insufficient Credits.');
        }
    }

    public function getHistory(Request $request)
    {
        $histories_query = DB::table('credit_histories');
        
        if($request->type)
            $histories_query->where('credit_histories.type', $request->type);
            
        if($request->to)
            $histories_query->where('credit_histories.to', $request->to);

        $histories = $histories_query->orderByDesc('created_at')->limit(20)->get();

        $histories->map(function ($history) {
            $history->from = User::find($history->from)->firstname.' '.User::find($history->from)->lastname;
            $history->to = User::find($history->to)->firstname.' '.User::find($history->to)->lastname;
            return $history;
        });
        
        return $histories;
    }
}
