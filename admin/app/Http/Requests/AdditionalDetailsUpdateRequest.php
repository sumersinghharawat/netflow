<?php

namespace App\Http\Requests;

use App\Models\SignupField;
use Illuminate\Foundation\Http\FormRequest;

class AdditionalDetailsUpdateRequest extends FormRequest
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
            'required.*' => 'required',
            'non_required' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'required.*.required' => 'This field is required.',
        ];
    }
}
