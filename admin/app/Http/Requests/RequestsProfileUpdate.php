<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsProfileUpdate extends FormRequest
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
            'common_id' => 'required|numeric',
            'logoutTime'              => 'required',
            'moduleStatus_id' => 'required',
            'age_limit' => 'required|numeric',
            'userConfig_id' => 'required',
            'user_name_type' => 'required|max:200',
            'username_length' => 'required',
            'prefix' => 'nullable',
            'password_policy_id' => 'nullable',
            'mixed_case' => 'nullable',
            'number' => 'nullable',
            'sp_char' => 'nullable',
            'min_password_length' => 'nullable',
            'two_factor' => 'nullable',
            'ageRestriction' => 'nullable',
            'login_unapproved' => 'nullable',
            'enable_policy' => 'nullable',
            'prefix_status' => 'nullable',
            'password' => 'required_if:enable_policy,1|array|min:2',
        ];
    }
}
