<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddEpinRequest extends FormRequest
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
            'username' => 'required',
            'amount' => 'required|gt:0|numeric',
            'count' => 'required|gt:0|numeric',
            'expiry' => 'required|after:today',
        ];
    }
}
