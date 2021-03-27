<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AreaPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return $this->user()->can('manage-areas');
        // return Gate::allows('manage-area', $post)
        return true;
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
                        'name' => 'required|string|max:100',
                        'cutoff_time' => 'required|integer|min:5|max:60',
                        'limit' => 'required|integer',
                        'logo' => 'max:256',
                        'address' => '',
                    ];
                }
            case 'PATCH': {
                    return [
                        'name' => 'required|string|max:100',
                        'cutoff_time' => 'required|integer|min:5|max:60',
                        'limit' => 'required|integer',
                        'logo' => 'max:256',
                        'address' => '',
                    ];
                }
            default:
                break;
        }
    }
}
