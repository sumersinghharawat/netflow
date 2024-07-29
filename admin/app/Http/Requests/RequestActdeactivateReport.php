<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestActdeactivateReport extends FormRequest
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
            'filter_type' => [
                'sometimes',
                Rule::notIn(['data_range']),
            ],

            // 'fromDate' => 'required_if:filter_type,==,custom',
            // 'toDate' => 'required_if:filter_type,==,custom|after:fromDate'
        ];
    }
}
