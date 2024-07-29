<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsCompensationUpdate extends FormRequest
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
            'value' => 'required',
            'name' => 'required',
            // 'plan_commission'              =>      'required',
            // 'sponsor_commission'           =>      'nullable',
            // 'rank_commission'              =>      'nullable',
            // 'referral_commission'           =>      'nullable',
            // 'matching_bonus'               =>      'nullable',
            // 'pool_bonus'                   =>      'nullable',
            // 'fast_start_bonus'             =>      'nullable',
            // 'performance_bonus'            =>      'nullable',
            // 'sales_Commission'             =>      'nullable'

        ];
    }
}
