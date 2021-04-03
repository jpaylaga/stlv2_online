<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserPost;
use Illuminate\Database\Eloquent\Collection;
use App\Area;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use App\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use GeneratorHelper;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    { }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('users');
    }

    /**
     * Show the user profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showProfile()
    {
        return view('user-profile');
    }

    public function manageCoordinators(){ return view('coordinators'); }
    public function manageTellers(){ return view('tellers'); }
    public function manageAreaAdmins(){ return view('area-admins'); }
    public function managePlayers(){ return view('players'); }

    public function getLoggedInUser(Request $request)
    {
        $user = $request->user();
        $user->area = '';
        if( $user->role == 'usher' || $user->role == 'teller' || $user->role == 'player' ){
            $coordinator = $user->parent()->first();
            $user->outlet = $user->outlets()->allRelatedIds()->first();
            $user->area = $coordinator->areas()->first();
        }else{
            $user->area = $user->areas()->first();
        }
        return $user;
    }

    public function get(Request $request)
    {
        
        $user = $request->user();
        $users = [];
        $_roles = array();
        $area = null;
        $with_areas = false;

        if($request->filled('user'))
            $user = User::find($request->user);

        if ($user->role === 'super_admin') {
            $where = [
                ['id', '!=', auth()->id()],
                ['role', '!=', 'super_admin']
            ];
            $users_query = User::where($where);
            $with_areas = true;

        } elseif ($user->role === 'coordinator') {
            $users_query = $user->children();
        } elseif ($user->role === 'area_admin') {
            $area = $user->areas()->first();
            // if ($request->filled('role')) {
            //     if( $request->role == 'teller' || $request->role == 'usher' ){
            //         $users_query = $area->areaAgents();
            //     }elseif( $request->role == 'coordinator' ){
            //         $users_query = $area->users();
            //     }
            // }else{
                $users_query = DB::table('users')
                    ->join('agent_area', 'users.id', '=', 'agent_area.user_id', 'left outer')
                    ->join('area_user', 'users.id', '=', 'area_user.user_id', 'left outer')
                    ->select('users.id','users.firstname','users.lastname','users.role','users.picture','users.username','users.percentage', 'users.is_active');
            // }
        }

        if( $area ){
            $users_query->where(function ($query) use ($area) {
                $query->where('agent_area.area_id', $area->id);
                $query->orWhere('area_user.area_id', $area->id);
            });
        }

        if ($request->filled('role')) {
            if( $request->role == 'teller' || $request->role == 'usher' )
                array_push($_roles, 'usher', 'teller');
            else
                array_push($_roles, $request->role);
        }

        // for options - if has type, reset _roles and get coordinator,usher,teller
        if( $request->filled('type') && $request->type == 'tellers' )
            $_roles = array('coordinator', 'usher', 'teller');
        
        if( $_roles )
            $users_query->whereIn('role', $_roles);

        // filter here
        if ($request->filled('name')) {
            $name = $request->name;
            $users_query->where(function ($query) use ($name) {
                $query->where('firstname', 'LIKE', '%' . $name . '%');
                $query->orWhere('lastname', 'LIKE', '%' . $name . '%');
                $query->orWhere('email', 'LIKE', '%' . $name . '%');
            });
        }
        
        // if ($request->filled('role') && $request->role != 'player') {
        //     $users_query->where('deleted', false);
        // }

        $users = $users_query->get();

        if ($request->filled('role') && $request->role == 'player') {
            $users = $users_query->with('parent')->get();
            return $users;
        }

        if( $with_areas ){
            $users = $users_query->with('areas', 'agentAreas')->get();
        }

        if ($request->filled('role') && $request->role == 'teller') {
            $users->map(function ($user) {
                $outlet = User::find($user->id)->outlets()->first();
                $user->outlet = $outlet ? $outlet->name : '';
                return $user;
            });
        }

        return $users;

    }

    public function getUser(User $user)
    {
        if ($user->role == 'area_admin' || $user->role == 'coordinator') {
            $user->area = $user->areas()->allRelatedIds()->first();
            $user->area_details = $user->areas()->first();
        } elseif ($user->role == 'teller' || $user->role == 'usher' || $user->role == 'player') {
            $user->coordinator = $user->parent()->allRelatedIds()->first();
            $user->outlet = $user->outlets()->allRelatedIds()->first();
        }

        $user->creditHistory = $user->creditLogs();
        $user->creditBalance = $user->creditBalance();

        return $user;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserPost $request)
    {
        $user = User::create($request->validated());

        // generate token
        $user->api_token = GeneratorHelper::generateRegCode();
        $user->save();
        
        $this->afterUserPost($request, $user);
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserPost $request, User $user)
    {
        $user->update($request->validated());
        $this->afterUserPost($request, $user);
        return $user;
    }

    public function afterUserPost($request, $user)
    {
        if ($request->filled('password'))
            $user->password = Hash::make($request->input('password'));

        $coordinator = User::find($request->input('coordinator'));

        if ($request->input('role') == 'coordinator' || $request->input('role') == 'area_admin') {
            // detach and set
            $user->areas()->detach();
            // AreaUser::where('user_id', $user->id)->delete();
            $user->areas()->attach( $request->input('area') );

            // detach and set area_admin
            $area_admin = Area::find($request->area)->users()->where('role', 'area_admin')->first();
            if( $request->input('role') == 'coordinator' && $area_admin ){
                $user->parent()->detach();
                $area_admin->children()->attach($user->id);
            }
            // $request->user()->children()->attach($user->id);
        } elseif ($request->input('role') == 'usher' || $request->input('role') == 'teller' || $request->input('role') == 'player') {
            // detach and set coordinator
            $user->parent()->detach();
            $coordinator->children()->attach($user->id);

            // detach and set area
            $area = $user->parent()->first()->areas()->first();
            $user->agentAreas()->detach($area->id);
            $user->agentAreas()->attach($area->id);
        }

        if ($request->input('role') == 'teller') {
            $user->outlets()->attach($request->input('outlet'));
        }

        if( $request->filled('is_active') )
            $user->is_active = $request->input('is_active');

        if( $request->filled('dob') )
            $user->dob = Carbon::parse( $request->input('dob') );

        $user->save();
    }

    public function addImage(Request $request)
    {
        $user = User::find($request->user);
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // $file_name = $user->username . $user->id . '.' . $request->image->extension();
            // $request->image->storeAs('public/profile_images', $file_name);
            // $user->picture = $file_name;

            $image = $request->file('image');
            $filename = $user->username . $user->id . '.' . $image->extension();

            $image_resize = Image::make($image->getRealPath());              
            $image_resize->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->crop(300, 300);

            $image_resize->save( storage_path('app/public/profile_images/' .$filename));
            $user->picture = $filename;

        }
        if( $user->save() )
            return 'success';
        return 'fail';
    }

    public function changeStatus(Request $request)
    {
        $user = User::find($request->user);
        $user->is_active = $request->action == 'activate' ? true : false;
        $user->save();
        return array('success'=>true);
    }

    public function changeCreditStatus(Request $request)
    {
        $user = User::find($request->user);
        $user->activate_credit = $request->action == 'activate' ? true : false;
        $user->save();
        return array('success'=>true);
    }

    public function updatePercentage(Request $request)
    {
        $user = User::find($request->user);
        $user->percentage = $request->percentage;
        $user->save();
        return array('success'=>true);
    }

    public function updateFloat(Request $request)
    {
        $user = User::find($request->user);
        $user->float = $request->float;
        $user->save();
        return array('success'=>true);
    }

    public function regenerateToken(User $user)
    {
        $user->api_token = GeneratorHelper::generateRegCode();

        $validator = Validator::make($user->toArray(), [
            'api_token' => 'min:12|unique:users',
        ]);

        if ($validator->fails()) {
            return 'Token exists, please try again.';
        }

        $user->save();
        return 'token generated: ' . $user->api_token;

    }

    public function getCreditBalance(User $user)
    {
        return array('credit_balance' => $user->creditBalance());
    }
}
