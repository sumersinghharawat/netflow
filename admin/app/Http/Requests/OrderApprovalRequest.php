<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderApprovalRequest extends FormRequest
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
            'order' => 'required|array|min:1',
            'order' => 'required|exists:orders,id|min:1',
        ];
    }
}
