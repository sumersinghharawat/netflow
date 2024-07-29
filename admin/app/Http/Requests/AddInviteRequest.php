<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddInviteRequest extends FormRequest
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
        if ($this->all()['type'] == 'banner') {
            return [
                'subject' => 'required|max:100|min:3',
                'content' => 'required|mimes:png,jpg,jpeg|max:2048',
                'type' => 'required|max:100',
                'target_url' => 'required|max:10000|url'
            ];
        } else {
            return [
                'subject' => 'required|max:100|min:3',
                'content' => 'required',
                'type' => 'required|max:100',
            ];
        }
    }
}
