<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestPackageNewPackage extends FormRequest
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
            'product_id' => 'sometimes|required|regex:/^\S*$/u|unique:packages,product_id,'.$this->id,
            'name' => 'sometimes|required',
            'price' => 'sometimes|required|numeric|gt:0',
            'validity' => 'sometimes|required|numeric',
            'pair_price' => 'sometimes|required|numeric',
            'pairValue' => 'sometimes|required|numeric|gte:0|unique:packages,pair_value,'.$this->id,
            'bvValue' => 'sometimes|required|numeric|gte:0',
            'validity' => 'sometimes|required|numeric|gt:0',
            'pairPrice' => 'sometimes|required|numeric|gte:0|unique:packages,price,'.$this->id,
            'levelCommission.*' => 'sometimes|required|numeric|gte:0|lte:100',
            'referralCommission' => 'sometimes|required|numeric|gt:0',
            'roi' => 'sometimes|required|numeric|gt:0',
            'days' => 'sometimes|required|numeric|gt:0',
            'description' => 'sometimes|required',
            'category' => 'sometimes|required',
            'image' => 'sometimes|mimes:png,jpeg,jpg|file|max:2048',
            'reentry_limit' => 'sometimes|required|numeric'
        ];
    }
}
