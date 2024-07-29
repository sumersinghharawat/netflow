<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinNumberRequset extends FormRequest
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
            'amount' => 'required|numeric|unique:pin_amount_details|gt:0',
        ];
    }
}
