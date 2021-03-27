<?php

namespace App\Http\Controllers;

use App\Http\Requests\OutletPost;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Requests\UserPost;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){ return view('outlets'); }

    /**
     * Get a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        if ($request->user()->role === 'super_admin') {
            return Outlet::where('is_active', true)->with('area')->get();
        } else {
            //temporarily get the outlet of a specific area. 
            //should return outlets based on area selected
            return $request->user()->areas()->first()->outlets;
        }
    }

    public function getOutlet(Outlet $outlet)
    {
        return $outlet;
    }

    /**
     * Show the form for storing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(OutletPost $request)
    {
        $outlet = Outlet::create($request->validated());
        return $outlet;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function show(Outlet $outlet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function edit(Outlet $outlet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function update(OutletPost $request, Outlet $outlet)
    {
        $outlet->update($request->validated());
        return $outlet;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Outlet $outlet)
    {
        //
    }
}
