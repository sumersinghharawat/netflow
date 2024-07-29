<?php

namespace App\Http\Requests;

use App\Models\SignupField;
use Illuminate\Foundation\Http\FormRequest;

class RequestcontactDetailsUpdate extends FormRequest
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
        $signupField = SignupField::MandatoryFields()->Active()->get();
        $rules = [];
        $rules['address']   = (!$signupField->where( 'name','address_line1')->isEmpty() && $signupField->where( 'name','address_line1')->first()->required)
                                ? 'required'
                                : 'nullable';
        $rules['address2']  = (!$signupField->where( 'name','address_line2')->isEmpty() && $signupField->where( 'name','address_line2')->first()->required)
                                ? 'required'
                                : 'nullable';
        $rules['city']      = (!$signupField->where( 'name','city')->isEmpty() && $signupField->where( 'name','city')->first()->required)
                                ? 'required'
                                : 'nullable';
        $rules['pin']       = (!$signupField->where( 'name','pin')->isEmpty() && $signupField->where( 'name','pin')->first()->required)
                                ? 'required'
                                : 'nullable';
        $rules['email']     = 'required';
        $rules['mob']       = 'required';
        $rules['phone']     = (!$signupField->where( 'name','phone')->isEmpty() && $signupField->where( 'name','phone')->first()->required)
                                ? 'required'
                                : 'nullable';
        $rules['country']     = (!$signupField->where( 'name','country')->isEmpty() && $signupField->where( 'name','country')->first()->required)
                                ? 'required'
                                : 'nullable';
        $rules['state']     = (!$signupField->where( 'name','state')->isEmpty() && $signupField->where( 'name','state')->first()->required)
                                ? 'required'
                                : 'nullable';
        return $rules;
    }
}
