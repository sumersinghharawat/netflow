<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CompanyLogos extends FormRequest
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
        $logType = $this->input('logoType');
        $imageRules = '';
        if ($logType === 'logo') {
            $imageRules = 'file|mimes:png,jpeg,jpg,gif,ico|max:2048|dimensions:height=44,width=200';
        } elseif ($logType === 'logo_dark') {
            $imageRules = 'file|mimes:png,jpeg,jpg,gif,ico|max:2048|dimensions:height=44,width=200';
        } elseif ($logType === 'shrink_logo') {
            $imageRules = 'file|mimes:png,jpeg,jpg,gif,ico|max:2048|dimensions:height=55,width=55';
        }
        elseif ($logType === 'favicon') {
            $imageRules = 'file|mimes:png,jpeg,jpg,gif,ico|max:2048|dimensions:width=32,height=32';
        }

        return [
            'logoType' => ['required', Rule::in(['logo_dark', 'logo', 'shrink_logo', 'favicon'])],
            'file' => $imageRules,
        ];
    }
}
