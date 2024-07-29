<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsPaymentGateway extends FormRequest
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
            'status_1' => 'nullable|numeric',
            'registartion_1' => 'nullable|numeric',
            'status_2' => 'nullable|numeric',
            'registartion_2' => 'nullable|numeric',
            'status_3' => 'nullable|numeric',
            'registartion_3' => 'nullable|numeric',
            'status_4' => 'nullable|numeric',
            'registartion_4' => 'nullable|numeric',
            'status_5' => 'nullable|numeric',
            'registartion_5' => 'nullable|numeric',
            'status_6' => 'nullable|numeric',
            'registartion_6' => 'nullable|numeric',
            'status_7' => 'nullable|numeric',
            'registartion_7' => 'nullable|numeric',
            'status_8' => 'nullable|numeric',
            'registartion_8' => 'nullable|numeric',
            'status_9' => 'nullable|numeric',
            'registartion_9' => 'nullable|numeric',
            'status_10' => 'nullable|numeric',
            'registartion_10' => 'nullable|numeric',
            'status_11' => 'nullable|numeric',
            'registartion_11' => 'nullable|numeric',

        ];
    }
}
