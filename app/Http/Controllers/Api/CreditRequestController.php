<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\CreditRequest;

class CreditRequestController extends Controller
{
    public function get(Request $request)
    {
        $user = $request->user();
        $params = $request->all(); 
        
        $query = CreditRequest::with('player','agent');
        $query->orderBy('status', 'desc')->orderBy('created_at', 'desc');

        if( $user->role == 'coordinator' ){
            $children = $user->children->pluck('id');
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
        if( $request->status == 'done' ){
            $result = $creditrequest->updateUserCredit();
            if( !$result ){
                return ['success' => false, 'message' => 'Something went wrong on updating user credit, please try again.'];
            }
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
