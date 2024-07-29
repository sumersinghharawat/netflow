<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestsbinaryConfigUpdate;
use App\Http\Requests\RequestsCommissionUpdate;
use App\Http\Requests\RequestsCompensationUpdate;
use App\Http\Requests\RequestslevelCommissionViewUpdate;
use App\Http\Requests\updateRoiCommissionRequest;
use App\Jobs\UserActivityJob;
use App\Models\Activity;
use App\Models\Addon;
use App\Models\BinaryBonus;
use App\Models\CommonSetting;
use App\Models\Compensation;
use App\Models\Configuration;
use App\Models\DemoUser;
use App\Models\GenelogyLevelCommission;
use App\Models\LevelCommissionRegisterPack;
use App\Models\MailGunConfiguration;
use App\Models\Mailsetting;
use App\Models\OCProduct;
use App\Models\Package;
use App\Models\PaymentGatewayConfig;
use App\Models\SignupSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SettingsController extends CoreInfController
{
    public function commissionSettings()
    {
        $configuration = $this->configuration();
        $moduleStatus = $this->moduleStatus();
        return view('admin.settings.commission.index', compact('configuration', 'moduleStatus'));
    }
    public function getUserActivity(Request $request){
        $moduleStatus   = $this->moduleStatus();
        if($request->has('username'))
        {
        $data = Activity::where('user_id', $request->username)->with('user');
        }
        else
        {
            $data = Activity::with('user');
        }
        $activity = $data->paginate(10)->withQueryString();
        return view('admin.settings.activities' , compact('moduleStatus' , 'activity'));
    }

    public function signupSettings($prefix = null)
    {
        $data = [
            'signupSetting' => SignupSetting::first(),
            'pendingSignUp' => PaymentGatewayConfig::where('slug', 'free-joining')->first(),
            'configuration' => $this->configuration(),
            'currency' => currencySymbol(),
        ];

        $moduleStatus = $this->moduleStatus();

        return view(
            'admin.settings.signup.index',
            compact('data', 'moduleStatus')
        );
    }

    public function update_signupSettings(Request $request)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }
        $request->validate([
            'reg_amount' => 'required|numeric|gte:0'
        ]);
        $moduleStatus = $this->moduleStatus();
        if (!$moduleStatus->product_status) {
            $request->validate([
                'reg_amount' => 'required|numeric|gt:0'
            ]);
        }

        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }
        PaymentGatewayConfig::where('slug', 'free-joining')
            ->update([
                'reg_pending_status' => $request->free_join ?? 0,
            ]);
        $registrationAmount = defaultCurrency($request->reg_amount);
        $config = Configuration::first();
        $config->reg_amount = $registrationAmount;
        $config->save();

        $signuSetting = SignupSetting::first();
        $signuSetting->update([
            'registration_allowed' => ($request->block) ? 0 : 1,
            'mail_notification' => $request->mail_notification ?? 0,
            'email_verification' => $request->email_verification ?? 0,
            'binary_leg' => $request->binary_leg ?? 'any',
            'email_verification' => $request->email_verification ?? 0,
        ]);
        $signuSetting = SignupSetting::first();
        $prefix = config('database.connections.mysql.prefix');

        $config = Configuration::first();
        Cache::forever("{$prefix}signupSettings", $signuSetting);
        Cache::forever("{$prefix}configurations", $config);

        return redirect(route('signup'))->with('success', 'settings updated successfully');
    }

    public function commissionUpdate(RequestsCommissionUpdate $request)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $configuration = Configuration::first();
            $configuration->update([
                'purchase_income_perc' => $request->purchase_wallet_commission,
                'service_charge' => $request->service_charge,
                'tds' => $request->tax,
                'trans_fee' => defaultCurrency($request->transaction_fee),
                'skip_blocked_users_commission' => $request->skip_blocked_users_commission ?? 0,
            ]);
            $configuration = Configuration::first();
            $user = auth()->user();
            $prefix = config('database.connections.mysql.prefix');
            Cache::forever("{$prefix}configurations", $configuration);
            $data = [];
            $data = $request->validated();

            if ($request->skip_blocked_users_commission) {
                $data['skip_blocked_users_commission'] = '1';
            } else {
                $data['skip_blocked_users_commission'] = '0';
            }
            UserActivityJob::dispatch(
                $user->id,
                $data,
                'commission settings change',
                $user->username . ' changed commission settings',
                $prefix,
                $user->user_type,
            );
            if (Cache::has("{$prefix}configurations")) {
                Cache::forever("{$prefix}configurations", $configuration);
            }

            return redirect(route('commission'))->with('success', 'commission updated successfully');
        } catch (\Exception $e) {
            return redirect(route('commission'))
                ->with('error', $e->getMessage());
        }
    }

    public function compensationSettings()
    {
        // dd();
        $compensation = $this->compensation();
        $moduleStatus = $this->moduleStatus();
        $configuration = $this->configuration();
        $addonCommissions = Addon::where('status', 1)->pluck('slug');

        return view(
            'admin.settings.compensation',
            compact('compensation', 'moduleStatus', 'configuration', 'addonCommissions')
        );
    }

    public function compensationUpdate(RequestsCompensationUpdate $request)
    {
        $validatedData = $request->validated();
        try {
            $compensation = Compensation::first();
            $compensation->update([$validatedData['name'] => $validatedData['value']]);
            $prefix = config('database.connections.mysql.prefix');
            if (Cache::has("{$prefix}compensation")) {
                Cache::forget("{$prefix}compensation");
                Cache::forever("{$prefix}compensation", $compensation);
            }
            $user = auth()->user();

            UserActivityJob::dispatch(
                $user->id,
                [],
                'compensation change',
                $user->username . ' changed ' . $validatedData['name'] . ' to ' . $validatedData['value'],
                $prefix,
                $user->user_type
            );
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'data' => 'Compensation updated successfully',
                    'value' => $validatedData['value'],
                ]);
            }

            return redirect(route('compensation'))->with('success', 'Compensation updated successfully');
        } catch (Throwable $th) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'data' => $th->getMessage(),
                ], 422);
            }

            return back()->with('error', $th->getMessage());
        }
    }

    public function Roicommission(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $configuration = $this->configuration();
        $days = collect([
            'sunday' => __('compensation.sunday'),
            'monday' => __('compensation.monday'),
            'tuesday' => __('compensation.tuesday'),
            'wednesday' => __('compensation.wednesday'),
            'thursday' => __('compensation.thursday'),
            'friday' => __('compensation.friday'),
            'saturday' => __('compensation.saturday'),
        ]);

        if ($moduleStatus->product_status == 1 || ($moduleStatus->ecom_status == 1 && $moduleStatus->ecom_status_demo == 1)) {
            if ($moduleStatus->ecom_status) {
                $packages = OCProduct::where('status', 1)->where('package_type', 'registration')->select('model', 'product_id', 'roi', 'days')->get();
            } else {
                $packages = Package::where('type', 'registration')->ActivePackage()->select('id', 'name', 'product_id', 'roi', 'days')->get();
            }
        }

        return view('admin.settings.roi-commission', (compact('packages', 'days', 'configuration', 'moduleStatus')));
    }

    public function updateRoicommission(updateRoiCommissionRequest $request)
    {
        $configuration = $this->configuration();
        if ($request->has('day') || $request->has('period')) {
            if ($request->day) {
                $day = implode(",", $request->day);
            } else {
                $day = null;
            }
            if ($request->period) {
                $period = $request->period;
            } else {
                $period = $configuration->roi_period;
            }
            $configuration->update([
                'roi_days_skip' => ($period == 'daily' && $day != '') ? implode(",", $day) : '',
                'roi_period' => $period,
            ]);
        }
        $moduleStatus = $this->moduleStatus();
        if ($moduleStatus->ecom_status) {
            $packages = OCProduct::select('product_id as id', 'days', 'roi')->where('status', 1)->where('package_type', 'registration')->get();
        } else {
            $packages = Package::ActiveRegPackage()->select('id', 'name', 'product_id', 'roi', 'days')->get();
        }
        foreach ($packages as $package) {
            if ($request->has('roi' . $package->id) || $request->has('days' . $package->id)) {
                if ($request->has('roi' . $package->id)) {
                    $roi = 'roi' . $package->id;
                    $roi = $request->$roi;
                } else {
                    $roi = $package->roi;
                }
                if ($request->has('days' . $package->id)) {
                    $days = 'days' . $package->id;
                    $days = $request->$days;
                } else {
                    $days = $package->days;
                }
                if ($moduleStatus->ecom_status) {
                    $data = OCProduct::where('product_id', $package->id)->first();
                    $data->days = $days;
                    $data->roi = $roi;
                    $data->push();
                } else {
                    $package->update([
                        'days' => $days,
                        'roi' => $roi,
                    ]);
                }
            }
        }
        return redirect(route('roicommission'))->with('success', 'Updated successfully');
    }

    public function binaryConfig()
    {
        $moduleStatus = $this->moduleStatus();
        if ($moduleStatus->ecom_status) {
            $package_list = OCProduct::where('status', 1)->where('package_type', 'registration')->get();
        } elseif ($moduleStatus->product_status) {
            $package_list = Package::ActiveRegPackage()->get();
        }
        $data = [
            'moduleStatus' => $moduleStatus,
            'binaryBonus' => BinaryBonus::first(),
            'package' => $package_list ?? [],
            'currency' => currencySymbol(),
        ];

        return view('admin.settings.compensation.binary.index', compact('data', 'moduleStatus'));
    }

    public function binaryConfigUpdate(RequestsbinaryConfigUpdate $request)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $binaryData = $request->validated();
            DB::beginTransaction();
            // TODO add employee activity table if auth user is employee
            $data = [];
            $binaryBonus = BinaryBonus::first();
            $moduleStatus = $this->moduleStatus();
            if ($moduleStatus->product_status || $moduleStatus->ecom_status) {
                if ($moduleStatus->ecom_status) {
                    $packages = OCProduct::where('status', 1)->where('package_type', 'registration')->get();
                } else {
                    $packages = Package::ActiveRegPackage()->get();
                }
                foreach ($packages as $key => $pack) {
                    if ($request->commission_type == 'flat') {
                        $pck_id = defaultCurrency($request->pck[$pack->id ?? $pack->product_id]);
                    } else {
                        $pck_id = $request->pck[$pack->id ?? $pack->product_id];
                    }

                    $pack->update([
                        'pair_price' => $pck_id,
                    ]);
                }

                if ($request->has('carry_forward')) {
                    $data = [
                        'flush_out_limit' => $request->flush_out_limit,
                        'flush_out_period' => ($request->calculation_period == 'instant') ? $request->flush_out_period : $request->calculation_period,
                    ];
                }

                // if ($request->commission_type == 'flat') {
                //     $pair_value  = defaultCurrency($request->pair_value);
                // } else {
                //     $pair_value  = $request->pair_value;
                // }
                $pair_value = $request->pair_value;
                $data += [
                    'calculation_criteria' => $request->calculation_criteria,
                    'point_value' => $request->point_value ?? $binaryBonus->point_value,
                    'calculation_period' => $request->calculation_period,
                    'pair_type' => $request->pair_type,
                    'commission_type' => $request->commission_type,
                    'pair_value' => $pair_value,
                    'carry_forward' => $request->carry_forward ?? 0,
                    'block_binary_pv' => $request->block_binary_pv ?? 0,
                ];
            } elseif (!$moduleStatus->product_status) {
                $data += [
                    'pair_commission' => $request->pair_commission,
                ];
            }
            $binaryBonus->update($data);
            DB::commit();
            $user = auth()->user();
            $prefix = config('database.connections.mysql.prefix');
            UserActivityJob::dispatch(
                $user->id,
                $binaryData,
                'compensation change',
                $user->username . ' changed Binary commission settings',
                $prefix,
                $user->user_type,
            );

            return redirect(route('binaryConfig'))
                ->with(
                    'success',
                    'Binary Config updated successfully'
                );
        } catch (Throwable $th) {
            DB::rollBack();
            return redirect(route('binaryConfig'))
                ->withErrors($th->getMessage());
        }
    }

    public function levelCommissionViewUpdate(RequestslevelCommissionViewUpdate $request, $id)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $levelCommission = GenelogyLevelCommission::find($id);
            $levelCommissionDetails = GenelogyLevelCommission::all();
            $levelCommissionCommDetails = LevelCommissionRegisterPack::all();
            foreach ($levelCommissionDetails as $item) {
                $str = 'level_percentage_' . $item->level_no;
                if ($request->has($str)) {
                    $level_pack = $request->$str;
                    $level = GenelogyLevelCommission::where('level', $item->level_no)->first();
                    if ($level) {
                        $level->level_percentage = $level_pack;
                        $level->save();
                    }
                }
            }
            foreach ($levelCommissionCommDetails as $item) {
                $reg_str = 'commission_' . $item->level . '_reg_product_' . $item->package_id;
                $mem_str = 'commission_' . $item->level . '_member_product_' . $item->package_id;
                if ($request->has($reg_str)) {
                    $level_reg_pack = $request->$reg_str;
                    $level = LevelCommissionRegisterPack::where([['level', $item->level], ['package_id', $item->package_id]])->first();
                    if ($level) {
                        $level->cmsn_reg_pack = $level_reg_pack;
                        $level->save();
                    }
                }
                if ($request->has($mem_str)) {
                    $level_mem_pack = $request->$mem_str;
                    $level = LevelCommissionRegisterPack::where([['level', $item->level], ['package_id', $item->package_id]])->first();
                    if ($level) {
                        $level->cmsn_member_pack = $level_mem_pack;
                        $level->save();
                    }
                }
            }

            return redirect(route('levelcommission'))
                ->with('success', 'level commission updated');
        } catch (Throwable $e) {
            return redirect(route('levelcommission'))
                ->with('error', $e->getMessage());
        }
    }

    public function viewMail()
    {
        $moduleStatus = $this->moduleStatus();
        $settingsData = Mailsetting::first();

        $mailgunData = MailGunConfiguration::first();

        return view('settings.commission.mailSetting', compact('settingsData', 'moduleStatus', 'mailgunData'));
    }

    public function updateMailSettings(Request $request)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }
        DB::beginTransaction();
        try {
            $field = Mailsetting::first();
            $field->smtp_authentication = $request->smtpAuthtype;
            $field->smtp_protocol = $request->smtpProtocol;
            $field->smtp_host = $request->smtpHost;
            $field->smtp_username = $request->smtpusername;
            $field->smtp_password = $request->smtppw;
            $field->smtp_port = $request->smtpport;
            $field->smtp_timeout = $request->smtptimeout;
            $field->reg_mailtype = $request->mailType;
            $field->save();
            DB::commit();

            return back()->with('success', 'Updated successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function getApiKey()
    {
        if (config('mlm.demo_status') == 'yes') {
            $prefix = config('database.connections.mysql.prefix');
            $prefix = ($prefix) ? Str::of($prefix)->explode('_')[0] : '';
            config(['database.connections.mysql.prefix' => '']);
            DB::purge('mysql');
            DB::connection('mysql');
            $detail = DemoUser::where('prefix', $prefix)->first();
            $api = $detail->api_key;
            config(['database.connections.mysql.prefix' => $prefix]);
            DB::purge('mysql');
            DB::connection('mysql');
        } else {
            $configuration = $this->configuration();
            $api = $configuration->api_key;
        }
        $moduleStatus = $this->moduleStatus();

        return view('settings.commission.api-key-configuration', compact('api', 'moduleStatus'));
    }

    public function updateApiKey(Request $request)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $user = auth()->user();
            if (config('mlm.demo_status') == 'yes') {
                $prefix = config('database.connections.mysql.prefix');
                $prefix = ($prefix) ? Str::of($prefix)->explode('_')[0] : '';
                config(['database.connections.mysql.prefix' => '']);
                DB::purge('mysql');
                DB::connection('mysql');
                $detail = DemoUser::where('prefix', $prefix)->first();
                $detail->update([
                    'api_key' => $request->apiKey,
                ]);
                UserActivityJob::dispatch(
                    $user->id,
                    [],
                    'compensation change',
                    $user->user_type,
                    $user->username . ' changed API key ',
                    $prefix
                );
            } else {
                $configuration = $this->configuration();
                $configuration->update([
                    'api_key' => $request->apiKey,
                ]);
                UserActivityJob::dispatch(
                    $user->id,
                    [],
                    'compensation change',
                    $user->user_type,
                    $user->username . ' changed API key ',
                    ''
                );
            }

            return back()->with('success', 'Updated successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function getDynamicSessionoutTime()
    {
        $timeoutTime = CommonSetting::first();
        return  response()->json([
            'time' => $timeoutTime->logout_time,
            'active' => $timeoutTime->active,
        ]);
    }
}
