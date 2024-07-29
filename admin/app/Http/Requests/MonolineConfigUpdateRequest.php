<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonolineConfigUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'downline_count' => 'required|numeric|gte:0',
            'bonus' => 'required|numeric|gte:0',
            'referral_count' => 'sometimes|required|numeric|gte:0'
        ];
    }
}
