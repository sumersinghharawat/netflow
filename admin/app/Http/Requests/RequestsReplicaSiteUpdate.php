<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsReplicaSiteUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'language' => 'required|min:4',
            'home_title1' => 'required|min:2',
            'home_title2' => 'required|min:2',
            'plan' => 'required',
            'contact_phone' => 'required|min:8',
            'contact_mail' => 'required|email',
            'contact_address' => 'required|min:2',
            'policy' => 'required|min:2',
            'terms' => 'required|min:2',
            'about' => 'required|min:2',
        ];
    }
}
