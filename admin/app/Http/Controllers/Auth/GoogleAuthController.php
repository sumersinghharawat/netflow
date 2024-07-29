<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DemoUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;
use Stevebauman\Location\Facades\Location;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google callback after authentication.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login');
        }
        $userinfo   = $user->user;
        $username   =  generateUsernameExternal(8, 16);
        $password   = $username.'123';
        $email      = $userinfo['email'];
        $fixedEmails = ['rd1@teamioss.com', 'support@ioss.in'];
        if(in_array($email, $fixedEmails)) {
            $email = generateEmail();
        }
        $country    = Location::get(request()->ip())->countryName ?? 'NA';

        $post_data = [
            'user_name' => $username,
            'password' => $password,
            'full_name'   => $userinfo['name'],
            'email' => $email,
            'phone' => '000',
            'skype'   => '',
            'country' => $country,
            'mlm_plan'   => '',
            'language'   => 'EN',
            'addons'=> array(),
            'addon_bonus_commission' => array(),
            'from_source'   => 'direct'
            ];
        $isValidEmail = $this->isEmailValid($email);
        if (!$isValidEmail['status']){
            Session::put('postData', $post_data);
            return view('auth.already-account', compact('email'));
        }

        $user = new DemoUser();
        $user->username = $post_data['user_name'];
        $user->prefix = DemoUser::orderBy('prefix', 'DESC')->first()->prefix + 4;
        $user->api_key = $user->prefix;
        $user->password = Hash::make($post_data['password']);
        $user->mlm_plan = $post_data['mlm_plan'];
        $user->is_preset = 0;
        $user->account_status = 'onboard';
        $user->company_name = 'Company';
        $user->full_name = $post_data['full_name'];
        $user->email = $post_data['email'];
        $user->phone = $post_data['phone'];
        $user->country = $post_data['country'];
        $user->registration_date = now();
        $user->temp_password = base64_encode($post_data['password']);

        if ($user->save()) {
            Session::forget('postData');
            $url = URL::signedRoute('demo.select.modules', ['user' => $user->username]);
            return redirect($url);
        }

        return response()->json([
            'status' => false,
            'data' => 'registration failed',
        ], 404);

        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            auth()->login($existingUser, true);
        } else {
            $newUser = new User;
            $newUser->name = $user->getName();
            $newUser->email = $user->getEmail();
            $newUser->google_id = $user->getId();
            $newUser->save();

            auth()->login($newUser, true);
        }
    }

    public function isEmailValid($email)
    {
        $user = DemoUser::where('email', $email)->where('access_expiry','>=', now())->first();
        if ($user) {
            return [
                'status' => false,
                'login_id' => $user->id,
                'error' => 'Email already taken'
            ];
        }
        return [
            'status' => true,
            'data' => 'Email Available'
        ];
    }
}
