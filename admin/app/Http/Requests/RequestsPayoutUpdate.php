<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsPayoutUpdate extends FormRequest
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
            'min_payout' => 'required|numeric|gte:0',
            'max_payout' => 'required|numeric|gte:0|gte:min_payout',
            'fee_mode' => 'required|string|in:percentage,flat',
            'fee_amount' => 'required|numeric|gte:0',
            'request_validity' => 'required|numeric|max:31',
            'release_type' => 'required|string|in:from_ewallet,ewallet_request,both',
            'mail_status' => 'sometimes|required|numeric',
        ];
    }
}
