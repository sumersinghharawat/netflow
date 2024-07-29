<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class updatePvRequest extends FormRequest
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
        $userid = $this->all()['user_id'];
        $user = User::select('personal_pv', 'id')->find($userid);
        $action = $this->all()['action'];

        if ($action == 'add') {
            return [
                'pv' => 'required|numeric|gt:0',
                'action' => 'required',
            ];
        } elseif ($action == 'deduct') {
            return [
                'pv' => 'required|numeric|lte:' . $user->personal_pv,
                'action' => 'required',
            ];
        }
        return [
            'pv' => 'required|numeric',
            'action' => 'required',
        ];
    }
}
