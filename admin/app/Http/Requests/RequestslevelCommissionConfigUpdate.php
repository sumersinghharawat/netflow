<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestslevelCommissionConfigUpdate extends FormRequest
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
            'commission_upto_level' => 'required|numeric|min:1',
            'xup_level' => 'sometimes|required|numeric|min:1',
            'level_commission_type' => 'sometimes|required',
            'level_commission_criteria' => 'sometimes|required',
        ];
    }
}
