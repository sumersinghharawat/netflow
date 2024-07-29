<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBankPaymentReceiptRequest;
use App\Http\Requests\RequestsUserRegister;
use App\Models\BankTransferSettings;
use App\Models\CompanyProfile;
use App\Models\Language;
use App\Models\Package;
use App\Models\PasswordPolicy;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentReceipt;
use App\Models\PendingRegistration;
use App\Models\RegisterJob;
use App\Models\SignupField;
use App\Models\SignupSetting;
use App\Models\SubscriptionConfig;
use App\Models\TermsAndCondition;
use App\Models\User;
use App\Models\UsernameConfig;
use App\Models\Letterconfig;
use App\Models\UsersRegistration;
use App\Services\PackageUpgradeService;
use App\Services\UserApproveService;
use App\Traits\UploadTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserRegisterController extends CoreInfController
{
    use UploadTraits;

    protected $serviceClass;

    public function __construct(UserApproveService $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    public function registerForm($placement = null, $position = null)
    {
        $moduleStatus = $this->moduleStatus();
        $signupSettings = $this->signupSettings();

        if (!$signupSettings->registration_allowed) {
            return redirect()->back()->withErrors('register.registration_not_allowed');
        }
        if ($moduleStatus->ecom_status) {
            $ecommerceUrl = config('services.ecom.url');
            $prefix       = Str::of(config('database.connections.mysql.prefix'))->split('/[\s_]+/')[0];
            $string       = getStoreString();
            return redirect()->to($ecommerceUrl . "index.php?route=account/login&db_prefix={$prefix}&string={$string}&username={$placement}&position={$position}&reg_from_tree=1&register=1");
        }

        $configuration = $this->configuration();
        $currency = currencySymbol();
        $regFromTree = 0;
        $data = collect([]);
        $placementDetails = null;
        $plan = $moduleStatus->mlm_plan;
        $lang = auth()->user()->default_lang ?? Language::where('status', true)->where('default', true)->first()->id;

        if (auth()->user()->user_type == 'employee') {
            $sponsor = User::where('user_type', 'admin')->with('userDetails')->first();
        } else {
            $sponsor = auth()->user()->load('userDetails');
        }
        $data->put('sponsor', $sponsor);

        if ($position && $placement) {
            $regFromTree = 1;
            $placementDetails = User::with('userDetails')->where('username', $placement)->first();
            if (!$placementDetails) {
                return redirect()->route('register.form');
            }
            if (in_array($plan, ['Unilevel', 'Stair_Step', 'Donation', 'Party'])) {
                if ($this->serviceClass->checkPositionAvailable($placementDetails->id, $position, $sponsor->id)) {
                    return redirect()->route('register.form')->withErrors('register.placment_is_not_available');
                }
                $data->put('sponsor', $placementDetails);
            }
            if ($plan == 'Binary') {
                if (!in_array($position, ['L', 'R'])) {
                    return redirect()->route('register.form');
                }
                if ($this->serviceClass->checkPositionAvailable($placementDetails->id, $position, $sponsor->id)) {
                    return redirect()->route('register.form')->withErrors('register.placment_is_not_available');
                }
            } elseif ($plan == 'Matrix') {
                $widthCeiling = $configuration->width_ceiling;
                if ($position > $widthCeiling) {
                    return redirect()->route('register.form');
                }
                if ($this->serviceClass->checkPositionAvailable($placementDetails->id, $position, $sponsor->id)) {
                    return redirect()->route('register.form')->withErrors('register.placment_is_not_available');
                }
            }
        } else {
            $placementDetails = $sponsor;
        }

        if ($moduleStatus['subscription_status']) {
            $subscriptionConfig = SubscriptionConfig::first();
            $current_date = date('Y-m-d H:i:s');
            if ($moduleStatus['subscription_status'] && $subscriptionConfig->reg_status == 1 && auth()->user()->user_type != 'admin') {
                $user_package_validity = $this->serviceClass->getUserProductValidity(auth()->user()->id);
                if ($user_package_validity < $current_date) {
                    return redirect(route('dashboard'))->with('error', 'subcription expired');
                }
            }
        }
        if ($moduleStatus['product_status']) {
            $products = Package::ActiveRegPackage()->get();
            $data->put('products', $products);
            $data->put('isProductAdded', $this->isProductAdded());
        }

        if ($moduleStatus['pin_status']) {
            $data->put('isPinAdded', $this->isPinAdded());
        }

        $registerAmount = $this->configuration()['reg_amount'] ?? 0;

        if ($registerAmount || $moduleStatus['product_status']) {
            $paymentGateWay = PaymentGatewayConfig::SortAscOrder()->Registration()->with('details')->whereNotIn('id',array(1,5,6,7))->get();
            foreach ($paymentGateWay as $paymentmethod){
                if($paymentmethod['slug'] == 'bank-transfer'){
                    $bankdetails = BankTransferSettings::first();
                }
            }
            $data->put('paymentGateWay', $paymentGateWay);

            if (count($paymentGateWay) == 0) {
                $data['paymentGateWay']->name = 'bank-trasnfer';
            }
        }else{
            $data->put('paymentGateWay', NULL);
            $paymentGateWay = PaymentGatewayConfig::SortAscOrder()->where('slug','free-joining')->Registration()->with('details')->get();
            $data->put('paymentGateWay', $paymentGateWay);
        }
        $data->put('terms', TermsAndCondition::first());
        $data->put('usernameConfig', UsernameConfig::first());
        $signupFields = SignupField::SortAscOrder()->Active()->with('customFieldLang')->get();
        if ($signupFields->contains('name', 'country')) {
            $data = $data->put('countries', $this->countries());
        }
        $data->put('customFields', $signupFields);
        $data->put('default_lang', $lang);
        $data->put('bankInfo', BankTransferSettings::first()->get('account_info'));
        $data->put('modulestatus', $moduleStatus);
        $data->put('signupSettings', $this->signupSettings());
        $data->put('regFromTree', $regFromTree);
        $data->put('placementDetails', $placementDetails);

        return view('register.userRegister', compact('data', 'registerAmount', 'position', 'currency' , 'bankdetails'));
    }

    public function userRegister(RequestsUserRegister $request)
    {
        $signupSettings = $this->signupSettings();
        if (!$signupSettings->registration_allowed) {
            return redirect()->back()->withErrors('register.registration_not_allowed');
        }

        DB::beginTransaction();
        try {
            $requestData = $request->except('_method', '_token');
            $validatedData = $request->validated();
            $pendingData = array_merge($validatedData, $requestData);
            $registerAmount = $this->configuration()['reg_amount'];
            $pendingData['totalAmount'] = $registerAmount;
            $moduleStatus = $this->moduleStatus();
            $usernameConfig = UsernameConfig::first();
            $signupSettings = $this->signupSettings();

            $pendingData['mlm_plan'] = $moduleStatus->mlm_plan;
            $pendingData['username_type'] = $usernameConfig->user_name_type;
            $pendingData['age_limit'] = $signupSettings->age_limit;
            $pendingData['default_country'] = $requestData['country'] ?? $signupSettings->default_country;
            $pendingData['reg_amount'] = $registerAmount;

            $mlmPlan = $moduleStatus->mlm_plan;

            if (in_array($mlmPlan, ['Unilevel', 'Stair_Step', 'Party', 'Donation'])) {
                $pendingData['placement_id'] = $validatedData['sponsor_id'];
                $pendingData['placement_username'] = $pendingData['sponsorName'];
                $pendingData['placement_fullname'] = $pendingData['sponsorFullname'];
            }

            $pendingData['product_amount'] = 0;
            $pendingData['product_pv'] = 0;
            if ($registerAmount > 0 || $moduleStatus->product_status) {
                if (!$moduleStatus->product_status) {
                    $pendingData['totalAmount'] = $registerAmount;
                    $pendingData['product_amount'] = 0;
                    $pendingData['product_pv'] = 0;
                } else {
                    $product = Package::findOrfail($pendingData['product_id']);
                    $pendingData['totalAmount'] = $registerAmount + $product->price;
                    $pendingData['product_amount'] = $product->price;
                    $pendingData['product_pv'] = $product->pair_value;
                }
                $paymentTypeId = $request->payment_method;

                $pendingSignupStatus = $this->serviceClass->getPendingSignupStatus($paymentTypeId);
                $emailVerificationStatus = $this->emailVerificationStatus();
                if ($pendingSignupStatus) {
                    $pendingData['date_of_joining'] = date('Y-m-d H:i:s');
                    $receipt = '';
                    $pendingUser = $this->serviceClass->addPendingRegistration($pendingData, $paymentTypeId, $emailVerificationStatus);

                    if ($pendingUser && $moduleStatus->product_status) {
                        $invoice_no = $this->serviceClass->generateSalesInvoiceNumber();
                        $this->serviceClass->addToSalesOrder($pendingData, $invoice_no, $pendingUser);
                    }

                    DB::commit();

                    return redirect()->route('register.preview', ['username' => $pendingData['username']])->with('success', __('register.registration_pending_successfully_username_is', ['username' => $pendingData['username']]));
                } else {
                    if ($pendingData['product_id'] != '') {
                        $servicePackageupgrade = new PackageUpgradeService;
                        $product_validity = $servicePackageupgrade->getPackageValidityDate($pendingData['product_id'], '', $moduleStatus);
                    } else {
                        $product_validity = '';
                    }
                    DB::commit();
                    $model  = new RegisterJob();
                    $model->data = json_encode($pendingData);
                    $model->status = 0;
                    $model->payment_method = $paymentTypeId;
                    $model->save();
                    $approveStatus = $this->serviceClass->confirmRegister($pendingData, $moduleStatus, $product_validity);
                    if ($approveStatus['status']) {
                        return redirect()->route('register.preview', ['username' => $pendingData['username']])->with('success', __('register.registration_completed_successfully_username_is', ['username' => $pendingData['username']]));
                    } else {
                        return redirect()->back()->withErrors("error.{$approveStatus['error']}");
                    }
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return redirect()->back()->withErrors('registration_failed');
        }
    }

    public function addPaymentReceipt(AddBankPaymentReceiptRequest $request)
    {
        $file = $request->file('reciept');
        $prefix = 'bnk_';
        $folder = 'reciept';
        //$model      = $pendingData;
        $fileName = $this->uploadBnkRcpt(compact('file', 'prefix', 'folder'));
        $host = request()->getSchemeAndHttpHost();

        if ($fileName) {
            PaymentReceipt::create([
                'receipt' => $host . "/storage/$folder/$fileName",
                'username' => $request->user_name,
                'type' => 'register',
            ]);

            return response()->json(['success' => 'Receipt added successfully.']);
        }
        return response()->json(['data' => 'Receipt upload failed.'], 404);
    }

    public function dummyUsers($count = null)
    {
        $this->serviceClass->addDummyUsers(100);

        return redirect()->route('dashboard')->with('success', 'Dummy users created successfully.');
    }

    public function preview($username)
    {
        $configurations = $this->configuration();
        $admin = User::GetAdmin();
        $language = Language::where('name_in_english', 'english')->first();
        $welcomeletter = Letterconfig::where('language_id',($admin->default_lang) ? $admin->default_lang : $language->id)->first();
        $pendingDetails = PendingRegistration::where('username', $username)->with('paymentGateway', 'RegistraionPackage')->first();
        $moduleStatus = $this->moduleStatus();
        $currency     = currencySymbol();
        $companyDetails = CompanyProfile::first();
        if (!empty($pendingDetails)) {
            $userDetails = json_decode($pendingDetails->data, true);
            return view('register.preview', compact('configurations', 'pendingDetails', 'companyDetails', 'userDetails', 'moduleStatus', 'currency' , 'welcomeletter'));
        } else {
            $registeredDetails = UsersRegistration::where('username', $username)->with('paymentGateway', 'RegistrationPackage', 'userDetail')->first();
            $user = User::where('username', $username)->with('userDetail', 'sponsor', 'package')->first();
            return view('register.preview', compact('configurations', 'pendingDetails', 'companyDetails', 'registeredDetails', 'user', 'moduleStatus', 'currency' , 'welcomeletter'));
        }
    }

    public function checkDob(Request $request)
    {
        $request->validate(['dob' => 'required']);
        if ($request->has('dob') && $request->dob != '') {
            $settings = SignupSetting::first();
            if ($settings->age_limit) {
                $ageLimit = Carbon::now()->subYears($settings->age_limit);
                $dob = Carbon::parse($request->dob);
                if ($dob->lte($ageLimit)) {
                    return response()->json([
                        'status' => true,
                    ]);
                }
                throw ValidationException::withMessages([
                    'dob' => __('register.age_should_be_atleast_years_old', ['age' => $settings->age_limit]),
                ]);
            }

            return response()->json([
                'status' => true,
            ]);
        }
    }

    // public function checkMobile(Request $request)
    public function checkMobile(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|unique:users,email',
        'mobile' => 'numeric',
    ]);
    $pendingregistrations = PendingRegistration::where('email', $request->email)
    ->where('status', '<>', 'rejected')
    ->first();
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    } elseif (isset($pendingregistrations)) {
        return response()->json(['email' => 'Email already in use'], 422);
    }
    return response()->json([
        'status' => true,
        'data' => []
    ]);
}


    public function checkUsername(Request $request)
    {
        $usernameConfig = UsernameConfig::first()->length;
        $usernameLength = Str::of($usernameConfig)->explode(';');
        $passwordPolicy = PasswordPolicy::first();
        $passwordRules = [];
        array_push($passwordRules, Password::min($passwordPolicy->min_length));

        if ($passwordPolicy->enable_policy) {
            if ($passwordPolicy->mixed_case) {
                array_push($passwordRules, Password::min($passwordPolicy->min_length)->mixedCase());
            }
            if ($passwordPolicy->number) {
                array_push($passwordRules, Password::min($passwordPolicy->min_length)->numbers());
            }
            if ($passwordPolicy->sp_char) {
                array_push($passwordRules, Password::min($passwordPolicy->min_length)->symbols());
            }
        }
        $request->validate([
            'username' => 'required|alpha_dash|between:' . $usernameLength[0] . ',' . $usernameLength[1],
            'password' => ['required', ...$passwordRules],
            'confirm' => 'required|same:password',
            'terms' => 'required|accepted',
        ]);
        $availabe = true;
        $checkPeding = PendingRegistration::where('username', $request->username)->where('status', '!=', 'rejected')->exists();
        $checkUsers = User::where('username', $request->username)->exists();
        if ($checkPeding || $checkUsers) {
            $availabe = false;
            throw ValidationException::withMessages([
                'username' => __('register.username_already_taken'),
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $availabe,
        ]);
    }

    public function storeRegister()
    {
        $storUrl = config('services.ecom.url');
        $prefix = session()->get('prefix');
        $string = getStoreString();
        return redirect()->to($storUrl . 'index.php?route=account/login&db_prefix=' . $prefix . '&string=' . $string . '&register=1');
    }

    public function store()
    {
        $storUrl = config('services.ecom.url');
        $prefix = session()->get('prefix');
        $string = getStoreString();
        return redirect()->to($storUrl . 'index.php?route=account/login&db_prefix=' . $prefix . '&string=' . $string . '&store=1');
    }
}
