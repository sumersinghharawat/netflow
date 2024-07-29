<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsCustomfieldUpdate extends FormRequest
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
        // foreach ($this->all()['field'] as $key => $value) {
        //     if (isset($value['is_required'])) {
        //         if(isset($value['is_enabled'])) return true;
        //         else return false;
        //     }
        // }
        // return true;
        return [
            'field.*.is_enabled' => 'required_with:field.*.is_required',
            // 'field.*.sortorder' => 'required|unique:signup_fields,sort_order,' . $this->id,
        ];
    }

    public function messages()
    {
        return [
            'field.*.is_enabled.required_with' => trans('common.field_shouldbe_enabled_before_mandatory')
        ];
    }
}
