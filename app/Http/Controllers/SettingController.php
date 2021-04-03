<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getSettingsByType(Request $request){
        $type = $request->type;
        $settings = Setting::where('type', $type)->get();
        return $settings;
    }

    public function postSettings(Request $request) {
        $data = $request->json()->all();
        foreach ($data as $field) {
            if( $this->has( str_slug( $field['name'], '_') ) ){
                $setting = Setting::where('slug', $field['slug'])->first();
                $setting->update([
                    'name' => $field['name'] ? $field['name'] : $setting->name,
                    'val' => $field['val'] ? $field['val'] : $setting->val,
                    'type' => $field['type'] ? $field['type'] : $setting->type,
                    'field_type' => $field['field_type'] ? $field['field_type'] : $setting->field_type,
                ]);
            }else{
                Setting::create([
                    'name' => $field['name'], 
                    'slug' => str_slug( $field['name'], '_'), 
                    'val' => $field['val'], 
                    'type' => $field['type'],
                    'field_type' => $field['field_type'],
                ]);
            }
        }
        return array('success' => true);
    }

    public function has($slug){
        return (boolean) Setting::whereSlug($slug)->count();
    }
}
