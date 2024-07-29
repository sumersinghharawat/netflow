<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsCommissionUpdate extends FormRequest
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
            'purchase_wallet_commission' => 'sometimes|required|numeric',
            'service_charge' => 'required|numeric',
            'tax' => 'required|numeric',
            'transaction_fee' => 'required|numeric',
        ];
    }
}
