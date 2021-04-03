<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\CreditReference;

class CreditReferenceController extends Controller
{
    public function list(Request $request)
    {
        $user = $request->user();
        $params = $request->all(); 
        
        $query = CreditReference::select('*');
        $query->orderBy('name', 'asc');

        if( $params['filter'] ){
            $filter = json_decode($params['filter']);

            if( isset( $filter->outlet ) && $filter->outlet )  $query->where('outlet',$filter->outlet);

            if( isset( $filter->mobile_number ) && $filter->mobile_number )  $query->where('mobile_number',$filter->mobile_number);

            if( isset( $filter->name ) && $filter->name )  $query->where('name',$filter->name);

            if( isset( $filter->ref_number ) && $filter->ref_number ) $query->where('ref_number', $filter->ref_number);

            if( isset( $filter->is_active ) && $filter->is_active ) $query->where('is_active', $filter->is_active);
        }

        return $query->paginate($request->per_page ?? 10);
    }

    public function get(CreditReference $reference)
    {
        return $reference;
    }

    public function update(CreditReference $reference, Request $request)
    {
        $payload = $request->all();
        if( $reference->update($payload) ){
            return ['success' => true, 'message' => 'Reference successfully updated.'];
        }
        return ['success' => false, 'message' => 'Unable to update reference.'];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref_number' => 'required',
            'mobile_number' => 'required',
            'name' => 'required',
            'outlet' => 'required',
        ]);

        if ($validator->fails()) return ['success' => false, 'message' => $validator->errors()];
        
        $payload = $request->all(); 

        $creditrequest = CreditReference::create($payload);

        return ['success' => true, 'message' => 'Request successfully sent.'];
    }

}
