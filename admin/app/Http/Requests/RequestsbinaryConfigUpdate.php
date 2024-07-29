<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsbinaryConfigUpdate extends FormRequest
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
            'calculation_criteria' => 'required|max:200',
            'calculation_period' => 'required|in:instant,daily,weekly,monthly',
            'pair_type' => 'required|max:200',
            'commission_type' => 'required|max:200',
            'pair_value' => 'required|numeric',
            'flush_out_limit' => 'required_if:carry_forward,yes',
            'pck.*' => 'sometimes|required|numeric',
            'flush_out_period' => 'required_if:carry_forward,yes',

        ];
    }
}
