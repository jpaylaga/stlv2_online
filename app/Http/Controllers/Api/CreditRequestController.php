<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\CreditRequest;
use App\CreditHistory;
use App\User;

class CreditRequestController extends Controller
{
    public function get(Request $request)
    {
        $user = $request->user();
        $params = $request->all(); 
        
        $query = CreditRequest::with('player','agent');
        $query->orderBy('status', 'desc')->orderBy('created_at', 'desc');

        if( $user->role == 'coordinator' ){
            $agents = $request->user()->children;
            $agents = $agents->merge(User::whereId($request->user()->id)->get());
            $children = $agents->pluck('id');

            // $children = $user->children->pluck('id');
            $query->whereIn('player_id', $children);
        }

        if( $params['filter'] ){
            $filter = json_decode($params['filter']);
            if( $filter->trans_type )
                $query->where('type',$filter->trans_type);
            if( $filter->player && $filter->player > 0 )
                $query->where('player_id',$filter->player);
            if( $filter->status )
                $query->where('status',$filter->status);
            if( $filter->ref )
                $query->where('ref',$filter->ref);
        }

        return $query->paginate($request->per_page ?? 10);
    }

    public function topup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref' => 'required',
            'outlet' => 'required',
            'sender' => 'required',
            'receiver' => 'required',
            'amount' => 'required|min:1',
            'player_id' => 'required',
        ]);

        if ($validator->fails()) return ['success' => false, 'message' => $validator->errors()];
        
        $user = $request->user;
        $payload = $request->all(); 

        $creditrequest = CreditRequest::create([
            'type' => 'deposit',
            'status' => 'pending',
            'ref' => $payload['ref'],
            'outlet' => $payload['outlet'],
            'amount' => $payload['amount'],
            'sender' => $payload['sender'],
            'receiver' => $payload['receiver'],
            'player_id' => $payload['player_id'],
            'user_id' => isset( $payload['user_id'] ) ? $payload['user_id'] : User::where('role', 'super_admin')->first()->id,
        ]);

        return ['success' => true, 'message' => 'Request successfully sent.'];
    }

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet' => 'required',
            'amount' => 'required|min:1',
            'player_id' => 'required',
        ]);

        if ($validator->fails()) return ['success' => false, 'message' => $validator->errors()];

        $payload = $request->all(); 
        $player = User::findOrFail($payload['player_id']);
        if( (int)$payload['amount'] > $player->creditBalance() ) return ['success' => false, 'message' => 'Insufficient credits.'];

        $creditrequest = CreditRequest::create([
            'type' => 'withdraw',
            'status' => 'pending',
            'outlet' => $payload['outlet'],
            'amount' => $payload['amount'],
            'receiver' => $payload['receiver'],
            'description' => $payload['description'],
            'player_id' => $payload['player_id'],
            'user_id' => isset( $payload['user_id'] ) ? $payload['user_id'] : User::where('role', 'super_admin')->first()->id,
        ]);

        // auto deduct on player withdraw even if request isnt confirmed
        CreditHistory::create([
            'type' => 'withdraw',
            'to' => $player->id,
            'from' => $player->id,
            'amount' => (int)$payload['amount'],
            'user_id' => $player->id,
        ]);

        // $result = $creditrequest->updateUserCredit();
        // if( !$result ){
        //     $creditrequest->delete();
        //     return ['success' => false, 'message' => 'Something went wrong on updating user credit, please try again.'];
        // }

        return ['success' => true, 'message' => 'Cash-out Request successfully sent.'];
    }

    public function updateStatus(Request $request, CreditRequest $creditrequest)
    {
        $user = $request->user();

        // authorize
        if( !$this->authorizeUser($user, $creditrequest) )
            return ['success' => false, 'message' => 'Unauthorized action.'];

        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) return ['success' => false, 'message' => 'Something went wrong, please try again.'];
        
        // credit update
        $wthdrwConfirm = $creditrequest->type == 'withdraw' ? true : false;
        if( $request->status == 'done' ){
            if( $creditrequest->type == 'deposit' ){
                // check if sufficient credits
                if( in_array($user->role, ['area_admin', 'coordinator']) && ( (int)$user->creditBalance() < (int)$creditrequest->amount ) ){
                    return ['success' => false, 'message' => 'You have insufficient credits to transfer to this account.'];
                }
                $result = $creditrequest->updateUserCredit();
                if( !$result ){
                    return ['success' => false, 'message' => 'Something went wrong on updating user credit, please try again.'];
                }
            }else{ //withdrawals
                
            }
        }elseif( $request->status == 'cancelled' && $creditrequest->type == 'withdraw' ){
            $result = $creditrequest->updateUserCredit(true); //update to refund 
        }

        $creditrequest->status = $request->status;
        $creditrequest->description = $request->notes;
        $creditrequest->save();

        return ['success' => true, 'message' => 'Successfully updated request!'];

    }

    // helpers
    public function authorizeUser($user, $creditrequest)
    {
        if( $user->role == 'super_admin' )
            return true;

        if( $user->id == $creditrequest->player->parent()->first()->id )
            return true;
        
        return false;
    }
}
