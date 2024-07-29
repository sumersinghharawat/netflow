<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CoreInfController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AdminRegisterController extends CoreInfController
{
    public function password(Request $request)
    {
        if(session()->get('is_preset')){
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }
        $userId = $request->userId ?? auth()->user()->id;
        $user = User::find($userId);
        if ($user->user_type == 'admin') {
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => trans('profile.incorrect_password'),
                ]);
            }
        }
        $request->validate([
            'current_password' => 'sometimes|required',
            'new_password' => 'required|confirmed|min:6'
        ]);
        $user->password = Hash::make($request->new_password);
        $user->push();
        $moduleStatus = $this->moduleStatus();

        if ($moduleStatus->ecom_status && $user->ecom_customer_ref_id) {
            DB::table('oc_customer')->where('customer_id', $user->ecom_customer_ref_id)->update([
                'password' => md5($request->new_password)
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => trans('profile.password_update_success'),
        ]);
    }
}
