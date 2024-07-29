<?php

namespace App\Http\Requests;

use App\Rules\TransactionPasswordCheck;
use Illuminate\Foundation\Http\FormRequest;

class FundTransferRequest extends FormRequest
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
            'transfer_from' => 'required|exists:users,id',
            'transfer_to' => 'required|exists:users,id|different:transfer_from',
            'amount' => 'required|min:1|numeric|max:100000000',
            'notes' => 'nullable|string',
            'transaction_password' => ['required_with_all:transfer_from,transfer_to', 'bail', new TransactionPasswordCheck($this->request->all())],
        ];
    }

    public function messages()
    {
        return [
            'transfer_from.required' => 'Transfer from User is required',
            'transfer_from.exists' => 'Not a valid User',
            'Transfer_to.required' => 'Transfer to user is required',
        ];
    }
}
