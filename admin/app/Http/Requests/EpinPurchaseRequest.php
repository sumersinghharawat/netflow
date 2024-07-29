<?php

namespace App\Http\Requests;

use App\Rules\PurchaseEpinUsernameRule;
use Illuminate\Foundation\Http\FormRequest;

class EpinPurchaseRequest extends FormRequest
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
            'username' => ['required', 'exists:users,id',  new PurchaseEpinUsernameRule],
            'purchase_amount' => 'required|gt:0|numeric',
            'purchase_count' => 'required|gt:0|numeric',
            'purchase_expiry' => 'required|after:today',
        ];
    }
}
