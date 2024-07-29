<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EpinTransferRequest extends FormRequest
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
            'from_user' => 'required',
            'to_user' => 'required', 'different:from_user',
            'epin' => 'required',
        ];
    }
}
