<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
                    'username' => 'required|string|max:50|unique:users',
                    'firstname' => 'required|string|max:128',
                    'lastname' => 'required|string|max:128',
                    'email' => 'required|string|email|unique:users',
                    'password' => 'required|string|min:6|confirmed',
                    'phone' => 'max:13',
                    'facebook' => '',
                    'gender' => '',
                    'address' => 'max:250',
                    'bod' => '',
                    'civil_status' => '',
                    'id_number' => 'max:100',
                    'phone' => 'max:13',
                    'role' => 'required',
                    'percentage' => '',
                    'float' => '',
                ];
            }
            case 'PATCH': {
                return [
                    'username' => 'required|string|max:50|unique:users,username,' . $this->route()->user->id,
                    'email' => 'required|email|unique:users,email,' . $this->route()->user->id,
                    'firstname' => 'required|string|max:128',
                    'lastname' => 'required|string|max:128',
                    'password' => $this->filled('password ') ? 'string|min:6|confirmed' : '',
                    'id_number' => 'max:100',
                    'facebook' => '',
                    'gender' => '',
                    'address' => 'max:250',
                    'bod' => '',
                    'civil_status' => '',
                    'phone' => 'max:13',
                    'role' => 'required',
                    'percentage' => '',
                    'float' => '',
                ];
            }
            default:
                break;
        }
        
    }
}
