<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailsettingsRequest extends FormRequest
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
            'mailType' => 'required|in:smtp,normal',
            'smtpAuthtype' => 'required_if:mailType,==,smtp',
            'smtpProtocol' => 'required_if:mailType,==,smtp',
            'smtpHost' => 'required_if:mailType,==,smtp',
            'smtpusername' => 'required_if:mailType,==,smtp',
            'smtppw' => 'required_if:mailType,==,smtp',
            'smtpport' => 'required_if:mailType,==,smtp',
            'smtptimeout' => 'required_if:mailType,==,smtp',

        ];
    }
}
