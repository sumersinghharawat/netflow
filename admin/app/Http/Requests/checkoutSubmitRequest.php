<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class checkoutSubmitRequest extends FormRequest
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
            'image' => 'file|size:2080',
            'image' => 'mimes:jpeg,jpg,png',
            'default_address' => 'required',
            'payment_method' => 'required',
            'epinOld' => 'required_if:payment_method,1',
            'tranPassword' => 'required_if:payment_method,2',
        ];
    }

    public function messages()
    {
        return [
            'epinOld.required_if' => 'Invalid Epin Details',
            'tranPassword.required_if' => 'Invalid Ewallet Details',
            'default_address.required' => 'You Must Choose an Address'
        ];
    }
}
