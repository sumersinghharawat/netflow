<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsMailBox extends FormRequest
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
            'send_status' => 'required|in:single_user,all',
            'user_id' => 'required_if:send_status,==,single_user',
            'subject' => 'required|max:500',
            'message' => 'required',

        ];
    }
}
