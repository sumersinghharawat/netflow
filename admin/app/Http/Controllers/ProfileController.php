<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\ModuleStatus;
use App\Models\CommonSetting;
use App\Models\SignupSetting;
use App\Models\PasswordPolicy;
use App\Models\UsernameConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\RequestsProfileUpdate;

class ProfileController extends CoreInfController
{
    public function index()
    {
        $data = [
            'commonSettings' => CommonSetting::first(),
            'moduleStatus' => $this->moduleStatus(),
            'signupSettings' => SignupSetting::first(),
            'usernameConfig' => UsernameConfig::first(),
            'passwordPolicy' => PasswordPolicy::first(),
        ];
        $length = $data['usernameConfig']->length;

        $minLength = explode(',', $length);

        return view('admin.settings.advancedSettings.profile.index', compact('data', 'minLength'));
    }

    public function update(RequestsProfileUpdate $request)
    {
        // dd($request);
        DB::beginTransaction();
        try {
            $prefix = session('prefix');
            $CommonSetting = CommonSetting::find($request->common_id);
            $CommonSetting->update([
                'logout_time' => $request->logoutTime ?? 0,
                'active' => ($request->enableAutoLogout == 'yes') ? 1 : 0,
            ]);
            $ModuleStatus = ModuleStatus::find($request->moduleStatus_id);

            $ModuleStatus->update([
                'google_auth_status' => $request->two_factor ?? 0,
            ]);
            $moduleStatus = ModuleStatus::first();
            Cache::forever("{$prefix}_moduleStatus", $moduleStatus);

            $SignupSetting = SignupSetting::first();

            $age = ($request->has('ageRestriction')) ? $request->age_limit : 0;

            $SignupSetting->update([
                'age_limit' => $age,
                'login_unapproved' => $request->login_unapproved ?? 0,
            ]);
            $userConfigData = [
                'length' => $request->username_length,
                'user_name_type' => $request->user_name_type,
                'prefix_status' => ($request->prefix_status == 'yes') ? 1 : 0,
            ];
            $UsernameConfig = UsernameConfig::find($request->userConfig_id);
            if ($request->has('prefix_status')) {
                $prefix = ($request->prefix_status) ? $request->prefix : '';
                $userConfigData['prefix'] = $prefix;
            }
            $UsernameConfig->update($userConfigData);

            $PasswordPolicy = PasswordPolicy::find($request->password_policy_id);
            if ($PasswordPolicy) {
                if ($request->min_password_length < 4) {
                    return redirect()->back()->withErrors('Minimum Password Length is 4');
                }
                $PasswordPolicy->update([
                    'min_length' => $request->min_password_length,
                ]);
                if ($request->enable_policy == 0) {
                    $PasswordPolicy->update([
                        'enable_policy' => 0,
                        'mixed_case' => 0,
                        'number' => 0,
                        'sp_char' => 0,
                    ]);
                } else {
                    $PasswordPolicy->update([
                        'enable_policy' => $request->enable_policy ?? 0,
                        'mixed_case' => $request->password['mixed_case'] ?? 0,
                        'number' => $request->password['number'] ?? 0,
                        'sp_char' => $request->password['sp_char'] ?? 0,
                        'min_length' => $request->min_password_length,
                    ]);
                }
            }
            DB::commit();

            return redirect(route('profile'))->with('success', 'profile updated succesfully');
        } catch (Throwable $th) {
            DB::rollBack();
            return redirect(route('profile'))
                ->with('error', $th->getMessage());
        }
    }
}
