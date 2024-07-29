<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyProfile extends FormRequest
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
            'name' => 'required|min:4',
            'address' => 'required|min:5',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'logo_dark' => 'sometimes|file|mimes:png,jpeg,jpg,gif,ico|max:2048|dimensions:height=44,width=200',
            'logo' => 'sometimes|file|mimes:png,jpeg,jpg,gif,ico|max:2048|dimensions:height=44,width=200',
            'shrink_logo' => 'sometimes|file|mimes:png,jpeg,jpg,gif,ico|max:2048|dimensions:height=55,width=55',
            'favicon' => 'sometimes|file|mimes:png,jpeg,jpg,gif,ico|max:2048|dimensions:width=16,height=16|dimensions:width=32,height=32|dimensions:width=48,height=48',
            //     // TODO add dimensions (width and height in validation)
        ];
    }
}
