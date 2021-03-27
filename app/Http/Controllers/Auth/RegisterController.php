<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use GeneratorHelper;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index()
    {
        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:3|unique:users',
            'firstname' => 'required|string|max:128',
            'lastname' => 'required|string|max:128',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('online-register', ['referral' => $request->referral])   
                ->withErrors($validator)->withInput();
        }
        
        // $user = User::create($request->all());
        $user = new User;
        $user->username = $request->username;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->email = $request->username.'@stlv2.com';
        $user->password = Hash::make($request->password);
        $user->address = $request->address;
        $user->role = 'player';
        $user->is_active = false;
        $user->deleted = true;
        $user->api_token = GeneratorHelper::generateRegCode();
        $user->save();

        if( $request->referral && $referrer = User::where('api_token', $request->referral)->first() ){
            $user->is_active = true;
            $user->deleted = false;
            $user->activate_credit = true;

            $referrer->children()->attach($user->id);
            $area = $referrer->parent()->first()->areas()->first();
            $user->agentAreas()->attach($area->id);

            $user->save();
        }
        return redirect()->route('thank-you', $user->id);
    }

    public function thankYou(User $user)
    {
        return view('auth.thank_you', compact('user'));
    }

}
