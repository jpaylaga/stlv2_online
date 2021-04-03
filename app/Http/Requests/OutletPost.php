<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutletPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.s
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('manage-users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST': {
                return [
                    'name' => 'required|string|max:100|unique:outlets',
                    'street' => 'required|max:200',
                    'barangay' => 'required|max:100',
                    'city' => 'required|max:100',
                    'zipcode' => 'required|max:6',
                    'province' => 'required|max:120',
                    'is_affiliated' => 'boolean',
                    'is_active' => 'boolean',
                    'area_id' => '',
                ];
            }
            case 'PATCH': {
                return [
                    'name' => 'required|string|max:100',
                    'street' => 'required|max:200',
                    'barangay' => 'required|max:100',
                    'city' => 'required|max:100',
                    'zipcode' => 'required|max:6',
                    'province' => 'required|max:120',
                    'is_affiliated' => 'boolean',
                    'is_active' => 'boolean',
                    'area_id' => '',
                ];
            }
            default:
                break;
        }
        

        
    }
}
