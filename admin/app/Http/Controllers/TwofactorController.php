<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;
use App\Models\UserDetail;
use Exception;
use App\Services\SendMailService;
use Illuminate\Support\Facades\URL;

class TwofactorController extends Controller
{
    public function index()
    {
        $google2fa      = new Google2FA();
        $user           = auth()->user();
        $companyDetails = CompanyProfile::first();
        $qrCode         = null;
        $secretCode     = null;
        $hasSecret      = false;
        if(!$user->goc_key || $user->goc_key == ''){
            $secretCode    = $google2fa->generateSecretKey();
            if ($secretCode) {
                $qrCodeUrl = $google2fa->getQRCodeUrl(
                    $companyDetails->name,
                    $companyDetails->email,
                    $secretCode
                );
                $qrCode     = QrCode::size(150)->generate($qrCodeUrl);
                $hasSecret  = true;
            }
        }
        return view('two-factor.index', compact('qrCode', 'companyDetails', 'secretCode', 'hasSecret'));
    }
    public function verifyCode(Request $request)
    {
        $google2fa      = new Google2FA();
        $validatedData = $request->validate([
            'code' => 'required',
            'secret_code' => 'sometimes|required'
        ]);
        $user       = auth()->user();
        $secretKey  = ($user->goc_key)
                        ? $user->goc_key
                        : $validatedData['secret_code'];
        $valid  = $google2fa->verifyKey($secretKey, $validatedData['code']);
        if($valid) {
            $user->goc_key = $secretKey;
            $user->save();
            return redirect()->intended(route('dashboard'));
        } else {
            throw ValidationException::withMessages([
                'code' => trans('common.entered_code_not_match'),
            ]);
        }
    }
    public function resetTwoFactor(Request $request){
        if (! $request->hasValidSignature()) {
            abort(403);
        }
        User::where('user_type','admin')->update(['goc_key' => null]);
        return redirect()->route('twoFA.success');
    }
    public function sendTwoFactorMail(){
        try{
            $admin_data = User::where('user_type','admin')->with('UserDetail')->first();
            if($admin_data->UserDetail->email){
                $sendDetails['email'] = $admin_data->UserDetail->email;
                $sendDetails['first_name'] = $admin_data->UserDetail->email ?? '';
                $sendDetails['last_name'] = $admin_data->UserDetail->email ?? '';
                $signedURL = URL::temporarySignedRoute(
                    'twofactor.reset', now()->addMinutes(15), [
                       'email' => $admin_data->UserDetail->email
                    ]
                 );
                $sendDetails['reset_url'] = $signedURL;

                $serviceClass = new SendMailService;
                $sendMail = $serviceClass->sendAllEmails("reset_googleAuth",'', $sendDetails);
                return back()->with('success',__('common.twoFA-mail-success'));
            }else{
                return back()->with('error',__('common.twoFA-invalid-email'));
            }
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }
    public function TwoFactorSuccess(){
        $companyDetails = CompanyProfile::first();
        return view('two-factor.reset-success',compact('companyDetails','companyDetails'));
    }
}
