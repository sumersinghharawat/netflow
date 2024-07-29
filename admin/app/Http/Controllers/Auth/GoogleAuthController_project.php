<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;


class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login');
        }
        $userinfo   = $user->user;
        $email      = $userinfo['email'];

        $existingUser = $this->checkEmail($email);
        if(!$existingUser->status){
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }
        auth()->login($existingUser['data'], true);

    }

    public function checkEmail($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return [
                'status' => false,
                'data' => $user,
            ];
        }
        return [
            'status' => true,
        ];
    }
}
