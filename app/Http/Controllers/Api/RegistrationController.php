<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use GeneratorHelper;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:128',
            'lastname' => 'required|string|max:128',
            'password' => 'required|string|min:6',
            'phone' => 'required|max:11',
            'address' => 'max:250',
        ]);

        if ($validator->fails()) {
            return array(
                'success' => false,
                'error' => $validator->errors()->first()
            );
        }

        // $user = User::create($request->all());
        $user = new User;
        $user->username = $request->phone;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->email = $request->phone.'@stlv2.com';
        $user->password = Hash::make($request->password);
        $user->address = $request->address;
        $user->role = 'teller';
        $user->is_active = false;
        $user->deleted = true;
        $user->api_token = GeneratorHelper::generateRegCode();
        $user->save();
        // $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        
        return array (
            'success' => true,
            'token' => $user->api_token
        ); 

    }

    public function confirm(Request $request)
    {
        $confirmer = $request->user();

        if( $confirmer->role == 'coordinator' ){
            $coordinator = $confirmer;
        }elseif( ($confirmer->role == 'super_admin' || $confirmer->role == 'area_admin') && $request->filled('coordinator') ){
            $coordinator = User::findOrFail($request->coordinator);
        }else{
            return array (
                'success' => false,
                'message' => 'Unauthorized action',
            ); 
        }

        $code = $request->code;
        $area = $coordinator->parent()->first()->areas()->first();

        $user = User::where('api_token', $code)
            ->where('is_active', false)
            ->where('deleted', true)
            ->where('role', 'teller')->first();

        if( $user ){
            $user->is_active = true;
            $user->deleted = false;
            $user->activate_credit = true;

            if( $user->save() ){
                $coordinator->children()->attach($user->id);
                $user->agentAreas()->attach($area->id);
            }

            return array (
                'success' => true,
                'message' => 'User activated succesfully!',
                'user' => $user
            );  
        }

        return array (
            'success' => false,
            'message' => 'No registered user found.',
        );  
        
    }
}
