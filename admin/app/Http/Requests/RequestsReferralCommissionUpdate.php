<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsReferralCommissionUpdate extends FormRequest
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
        $rule = [
            'referral_commission_type' => 'sometimes|required|in:flat,percentage',
            'sponsor_commission_type' => 'sometimes|required|max:200',
            'product.*' => 'sometimes|required|numeric',
            'rank.*' => 'sometimes|required|numeric',

        ];
        // if ($this->sponsor_commission_type == 'rank') {
        //     $rule['referral_commission_type'] = 'required|in:flat';
        // }

        return $rule;
    }

    public function messages()
    {
        return [
            'referral_commission_type.in' => 'referral_commission criteria based on sponsor pack olny available in falt mode',
        ];
    }
}
