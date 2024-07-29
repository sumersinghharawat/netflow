<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FundCreditRequest extends FormRequest
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
            'username' => 'required|exists:users,id',
            'amount' => 'required|min:1|numeric|max:100000000',
            'notes' => 'nullable|string',
        ];
    }
}
