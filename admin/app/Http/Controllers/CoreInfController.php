<?php

namespace App\Http\Controllers;

use App\Models\Compensation;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\CrmLead;
use App\Models\CurrencyDetail;
use App\Models\DemoUser;
use App\Models\LevelCommissionRegisterPack;
use App\Models\ModuleStatus;
use App\Models\OCProduct;
use App\Models\Package;
use App\Models\PaymentGatewayConfig;
use App\Models\PerformanceBonus;
use App\Models\PinAmountDetails;
use App\Models\PinConfig;
use App\Models\PinNumber;
use App\Models\Rank;
use App\Models\RankConfiguration;
use App\Models\ReplicaContent;
use App\Models\SignupField;
use App\Models\SignupSetting;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CoreInfController extends Controller
{
    public function moduleStatus()
    {
        // $this->cacheClear('moduleStatus');
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_moduleStatus")) {
            $moduleStatus = Cache::get("{$prefix}_moduleStatus");
        } else {
            $moduleStatus = ModuleStatus::first();
            Cache::forever("{$prefix}_moduleStatus", $moduleStatus);
        }

        return $moduleStatus;
    }

    public function package()
    {
        $package = Package::get()->all();

        return $package;
    }

    public function compensation()
    {
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_compensation")) {
            $compensation = Cache::get("{$prefix}_compensation");
        } else {
            $compensation = Compensation::first();
            Cache::forever("{$prefix}_compensation", $compensation);
        }

        return $compensation;
    }

    public function configuration()
    {
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_configurations")) {
            $configuration = Cache::get("{$prefix}_configurations");
        } else {
            $configuration = Configuration::first();
            Cache::forever("{$prefix}_configurations", $configuration);
        }

        return $configuration;
    }

    public function getLevelSettingsMemberPack()
    {
        $commissionArray = [];
        $levelCommission = LevelCommissionRegisterPack::LevelAscOrder()->get();
        foreach ($levelCommission as $data) {
            if (array_key_exists($data->level, $commissionArray)) {
                $commissionArray[$data->level][$data->package_id . '_commission'] = $data->cmsn_member_pack;
            } else {
                $commissionArray[$data->level] = [
                    'id' => $data->id,
                    'level' => $data->level,
                    $data->package_id . '_commission' => $data->cmsn_member_pack,
                ];
            }
        }

        return $commissionArray;
    }

    public function rankConfig()
    {
        $rankConfig = RankConfiguration::first();

        return $rankConfig;
    }

    public function selectPackageRankConfig()
    {
        $moduleStatus = $this->moduleStatus();

        if (!$moduleStatus['ecom_status'] || !$moduleStatus['ecom_status_demo']) {
            $package = Package::ActiveRegPackage()->get(['product_id', 'name', 'id'])->all();
        } else {
            return OCProduct::where('status', 1)->where('package_type', 'registration')->get();
        }

        return $package;
    }

    public function addonModule()
    {
        //TODO find ADDON_MODULES keyword in Core_inf_controller and add functionalities
        return true;
    }

    public function getActiveRankDetails()
    {
        $query = Rank::where([['status', 1]])->with('rankDetails')->get();

        return $query;
    }

    public function signupSettings($prefix = null)
    {
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_signupSettings")) {
            $moduleStatus = Cache::get("{$prefix}_signupSettings");
        } else {
            $moduleStatus = SignupSetting::first();
            Cache::forever("{$prefix}_signupSettings", $moduleStatus);
        }

        return $moduleStatus;
    }

    public function countries($prefix = null)
    {
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_countries")) {
            $countries = Cache::get("{$prefix}_countries");
        } else {
            $countries = Country::select('id', 'name')->with('states')->NameAscOrder()->get();
            Cache::forever("{$prefix}_countries", $countries);
        }
        // $countries = Country::select('id', 'name')->with('states')->NameAscOrder()->cursor();
        return $countries;
    }

    public function isProductAdded()
    {
        return (Package::ActiveRegPackage()->exists()) ? 'yes' : 'no';
    }

    public function isPinAdded()
    {
        $flag = 'no';
        $date = now();

        $epin = PinNumber::where([
            ['status', 'yes'],
            ['expiry_date', '>', $date],
        ])->count();

        if ($epin > 0) {
            $flag = 'yes';
        }

        return $flag;
    }

    public function stateStatus()
    {
        $state = SignupField::where('name', 'state')->first();

        return $state;
    }

    public function getPendingSignupStatus($id)
    {
        $status = PaymentGatewayConfig::find($id);

        if ($status->slug == 'bank-trasfer') {
            return 1;
        }

        return $status->reg_pending_status;
    }

    public function emailVerificationStatus()
    {
        $prefix = session()->get('prefix');
        $status = $this->signupSettings($prefix)['email_verification'];

        return $status;
    }

    public function getPerformanceBonusTypes()
    {
        $list = [];

        $performance_bonus = PerformanceBonus::all();
        foreach ($performance_bonus as $row) {
            $list[] = $row['bonus_name'];
        }

        return $list;
    }

    public function getEpinConfig()
    {
        $config = PinConfig::first();

        return $config;
    }

    public function getUsers(Request $request)
    {
        if ($request->has('term')) {
            $string = $request->term;
            $users = User::Like($string)->GetUsers()->all();

            return response()->json([
                'status' => true,
                'data' => $users,
            ]);
        }
    }

    public function getPinAmounts(Request $request)
    {
        if ($request->has('term')) {
            $string = $request->term;
            $amount = PinAmountDetails::Like($string)->get()->all();

            return response()->json([
                'status' => true,
                'data' => $amount,
            ]);
        }
    }

    public function getEmployees(Request $request)
    {
        if ($request->has('term')) {
            $string = $request->term;
            $employees = User::where('user_type', 'employee')->where('active', true)->Like($string)->get()->all();

            return response()->json([
                'status' => true,
                'data' => $employees,
            ]);
        }
    }

    public function getEpin(Request $request)
    {
        if ($request->has('term')) {
            $string = $request->term;
            $epins = PinNumber::Like($string)->get()->all();

            return response()->json([
                'status' => true,
                'data' => $epins,
            ]);
        }
    }

    public function getTickets(Request $request)
    {
        if ($request->has('term')) {
            $string = $request->term;
            $tickets = Ticket::Like($string)->get()->all();

            return response()->json([
                'status' => true,
                'data' => $tickets,
            ]);
        }
    }

    public function cacheClear($key)
    {
        Cache::forget($key);
    }

    public function getLeadCompleteness($id)
    {
        $lead = CrmLead::where('id', $id)->first();
        $percentage = 30;
        if ($lead->last_name != '') {
            $percentage = $percentage + 10;
        }
        if ($lead->email_id != '') {
            $percentage = $percentage + 15;
        }
        if ($lead->mobile_no != '') {
            $percentage = $percentage + 15;
        }
        if ($lead->skype_id != '') {
            $percentage = $percentage + 15;
        }
        if ($lead->country_id != '') {
            $percentage = $percentage + 15;
        }

        return $percentage;
    }

    public function changeConfigLanguage()
    {
    }

    public function insertTransaction($transactionNumber)
    {
        $transaction = new Transaction();
        $transaction->transaction_id = $transactionNumber;
        $transaction->save();

        return $transaction->id;
    }

    public function getpolicyandTerms($id)
    {
        $userid = User::where('username', $id)->first();

        $replicacontent = ReplicaContent::where('user_id', $userid->id)->get();
        if ($replicacontent->isEmpty()) {
            $replicacontent = ReplicaContent::where('user_id', null)->get();
        } else {
            $replicacontent = ReplicaContent::where('user_id', $userid->id)->get();
        }
        $data = [];
        foreach ($replicacontent as $key => $value) {
            $data[$value->key] = $value->value;
        }

        return $data;
    }

    public function getDefaultCurrency()
    {
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_default_currency")) {
            $currency = Cache::get("{$prefix}_default_currency");
        } else {
            $currency = CurrencyDetail::where('default', 1)->first();
            Cache::forever("{$prefix}_default_currency", $currency);
        }
        return $currency;
    }
    public function getPresetCountries()
    {
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_countries")) {
            $countries = Cache::get("{$prefix}_countries");
        } else {
            $countries = Country::select('id', 'name')->NameAscOrder()->get();
            Cache::forever("{$prefix}_countries", $countries);
        }

        $view = view('ajax.demo.country', compact('countries'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ]);
    }
}
