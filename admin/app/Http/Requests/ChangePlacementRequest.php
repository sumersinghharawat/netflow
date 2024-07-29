<?php

namespace App\Http\Requests;

use App\Http\Controllers\CoreInfController;
use App\Rules\isAdmin;
use Illuminate\Foundation\Http\FormRequest;

class ChangePlacementRequest extends FormRequest
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
            'placement' => 'required|exists:App\Models\User,id',
        ];
        $coreController = new CoreInfController;
        $plan = $coreController->moduleStatus()->mlm_plan;
        if ($plan == 'Binary') {
            $rule['position'] = 'required';
        }

        return $rule;
    }
}
