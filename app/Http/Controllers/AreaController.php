<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Area;
use App\Betlimit;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AreaPost;
use Illuminate\Auth\Access\Gate;
use App\Price;

class AreaController extends Controller
{
    
    /**
     * Show the area settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function settings()
    {
        return view('area-settings');
    }

    public function manage()
    {
        return view('area-manage');
    }
    
    public function get(Request $request)
    {
        if ($request->user()->role === 'super_admin') {
            return Area::all();
        } else {
            return $request->user()->areas;
        }
    }

    public function getArea($area)
    {
        return Area::find($area);
    }

    public function getAreaWithPrices($area)
    {
        return Area::with( array('prices.game' => function($query){
            $query->select('id', 'name');
        }))->where('id', $area)->first();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\AreaPost  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AreaPost $request)
    {
        // $area = Area::create($request->validated());
        // $this->afterUserPost($request, $area);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(AreaPost $request, Area $area)
    {
        $area->update($request->validated());
        // save pricings
        foreach ( $request->prices as $_price) {
            $price = Price::find($_price['id']);
            $price->price_per_unit = $_price['price_per_unit'];
            $price->bet_limit = $_price['bet_limit'];
            $price->save();
        }
        // save bet limits
        $area = $this->getRequestorArea($request->user());
        Betlimit::where('area_id', $area->id)
            ->update(['limit' => $request->limit]);
    }

    public function addImage(Request $request)
    {
        $file = $request->file('file');
        $dir = 'public/images';
        // $path = $file->storeAs( $dir, $file->getClientOriginalName() );
        $path = $file->store( $dir );
        return str_replace("$dir/", '', $path);
    }

    public function getRequestorArea($user)
    {
        if ($user->role == 'super_admin') {
            // ***TEMP - this serves as a display only
            $area = Area::all()->first();
        } elseif ($user->role == 'area_admin') {
            $area = $user->areas()->first();
        } elseif ($user->role == 'coordinator') {
            $area = $user->areas()->first();
        } elseif ($user->role == 'usher' || $user->role == 'teller') {
            $area = $user->agentAreas()->first();
        }
        return $area;
    }

}
