<?php

namespace App\Http\Requests;

use App\Models\DemoUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AddBankPaymentReceiptRequest extends FormRequest
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
    public function rules(Request $request)
    {
        if ($this->route()->action['prefix'] == 'replica') {
            $validUser = $this->user;
            if (! $validUser) {
                throw ValidationException::withMessages([
                    'username' => __('auth.failed'),
                ]);
            }
            $demoUser = DemoUser::where('username', $validUser)->first();
            if (! $demoUser) {
                abort(401);
            }
            $prefix = $demoUser->prefix.'_';
        } else {
            $prefix = config('database.connections.mysql.prefix');
        }
        $rule = [
            'reciept' => 'required|file|max:2048',
            'reciept' => 'mimes:jpeg,png,jpg',
            'user_name' => 'required',
        ];

        return $rule;
    }
}
