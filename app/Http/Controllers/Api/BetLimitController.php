<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Betlimit;

class BetLimitController extends Controller
{

    public function store(Request $request)
    {
        $combination = $request->combination;
        $bet_type = $request->type;
        $limit = $request->limit ? $request->limit : 0;
        $winning = $request->winning;
        $area = $request->filled('area') ? $request->area : Area::all()->first()->id;

        $_limit = Betlimit::where([
            'combination' => $combination,
            'type' => $bet_type,
            'area_id' => $area
        ])->first();

        if( $_limit ){
            if( $limit > 0 )
                $_limit->limit = $limit;
            if( $winning )
                $_limit->winning = $winning;
            $_limit->save();
        }else{
            $bet_limit = new Betlimit();
            $bet_limit->combination = $combination;
            $bet_limit->type = $bet_type;
            $bet_limit->area_id = $area;
            $bet_limit->limit = $limit;
            $bet_limit->winning = $winning;
            $bet_limit->save();
        }

        return array('success' => true);
    }

    public function remove(Request $request)
    {
        $betlimit = Betlimit::findOrFail($request->combi_id);
        $betlimit->delete();  
    }
}
