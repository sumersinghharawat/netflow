<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsRepurchasepackageStore extends FormRequest
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
            'packageId' => 'required|max:200',
            'name' => 'required|max:200',
            'catogery' => 'required|numeric',
            'amount' => 'required|numeric',
            'pv' => 'required|numeric',
            'product_image' => 'sometimes|mimes:png,jpeg,jpg,gif,ico',
            'description' => 'required|max:2000',

        ];
    }
}
