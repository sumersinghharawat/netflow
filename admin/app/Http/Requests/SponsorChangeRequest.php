<?php

namespace App\Http\Requests;

use App\Rules\isAdmin;
use Illuminate\Foundation\Http\FormRequest;

class SponsorChangeRequest extends FormRequest
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
        $rule = [
            'user' => ['required', 'exists:App\Models\User,id', new isAdmin],
            'new_sponsor' => 'required|exists:App\Models\User,id',
        ];

        return $rule;
    }
}
