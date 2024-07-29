<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBankPaymentReceiptRequest;
use App\Http\Requests\ReplicacontactRequest;
use App\Http\Requests\ReplicaUserRegisterRequest;
use App\Models\CompanyProfile;
use App\Models\Contacts;
use App\Models\DemoUser;
use App\Models\ModuleStatus;
use App\Models\Package;
use App\Models\PasswordPolicy;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentReceipt;
use App\Models\PendingRegistration;
use App\Models\PinNumber;
use App\Models\ReplicaBanner;
use App\Models\ReplicaContent;
use App\Models\SignupField;
use App\Models\TermsAndCondition;
use App\Models\User;
use App\Models\UsernameConfig;
use App\Services\EwalletService;
use App\Services\ReplicaService;
use App\Services\StripeService;
use App\Services\UserApproveService;
use App\Traits\UploadTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use stdClass;
use Throwable;

class ReplicaController extends CoreInfController
{
    use UploadTraits;

    protected $serviceClass;

    protected $ewalletService;

    protected $replicaService;

    public function __construct(UserApproveService $serviceClass, EwalletService $ewalletService, ReplicaService $replicaService)
    {
        $this->serviceClass = $serviceClass;
        $this->ewalletService = $ewalletService;
        $this->replicaService = $replicaService;
    }

    public function index(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }
        $segments = Str::of($request->path())->explode('/');

        if (count($segments) != 2 && $segments[0] != 'replica') {
            abort(401);
        }
        $replicaUser = $segments[1];
        $prefix = '';
        if (config('mlm.demo_status') == 'yes') {
            $prefix = $this->replicaService->getprefix($replicaUser);
            config(['database.connections.mysql.prefix' => "{$prefix}_"]);
            DB::purge('mysql');
            DB::connection('mysql');
            $moduleStatus = ModuleStatus::first();
        } else {
            $moduleStatus = ModuleStatus::first();
        }
        if (! $moduleStatus->replicated_site_status) {
            abort(401);
        }

        $user = User::with('userDetail')->where('username', $replicaUser)->first();

        session()->push('lcp_session', [
            'prefix' => $prefix,
            'user_id' => $user->id,
            'username' => $user->username,
        ]);
        $banner = ReplicaBanner::where('user_id', $user->id)->where('is_default', 0)->get();

        if ($banner->isEmpty()) {
            $banner = ReplicaBanner::where('user_id', null)->where('is_default', '1')->get();
        }

        $replicacontent = ReplicaContent::where('user_id', $user->id)->get();

        if ($replicacontent->isEmpty()) {
            $replicacontent = ReplicaContent::where('user_id', null)->get();
        } else {
            $replicacontent = ReplicaContent::where('user_id', $user->id)->get();
        }
        $data = [];
        foreach ($replicacontent as $key => $value) {
            $data[$value->key] = $value->value;
        }

        $company = CompanyProfile::first();
        $replicaurl = URL::signedRoute('replica', ['replica' => $replicaUser]);

        // dd($banner);
        return view('replica.index', compact('user', 'company', 'data', 'banner', 'replicaurl'));
    }

    public function replicacontact(ReplicacontactRequest $request)
    {
        $prefix = '';
        $prefix = $this->replicaService->getprefix($request->user);

        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::purge('mysql');
        DB::connection('mysql');

        $user_id = User::select('id')->where('username', $request->user)->first();
        DB::beginTransaction();
        try {
            $user = new Contacts();
            $user->owner_id = $user_id['id'];
            $user->name = $request->name;
            $user->email = $request->Email;
            $user->address = $request->Address;
            $user->phone = $request->Phone;
            $user->contact_info = $request->Message;
            $user->mail_added_date = date('Y-m-d H:i:s');
            $user->status = 'yes';
            $user->read_msg = 'no';

            $user->save();
            $replicaurl = URL::signedRoute('replica', ['replica' => $request->user]);
            $url = $replicaurl.$request->url;
            DB::commit();

            return redirect($url)->with('success', __('replica.will_contact'));
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect($url)->with('error', $th->getMessage());
        }
    }

    public function getPolicy(Request $request, $replica)
    {
        $prefix = '';
        $demoUser = DemoUser::where('username', $replica)->first();

        if (! $demoUser) {
            abort(401);
        }
        $prefix = $demoUser->prefix;
        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::purge('mysql');
        DB::connection('mysql');
        $user = User::with('userDetail')->where('username', $demoUser->username)->first();
        $company = CompanyProfile::first();

        $replicacontent = $this->getpolicyandTerms($replica);

        return view('replica.policy', compact('replicacontent', 'user', 'company'));
    }

    public function userregistrationForm(Request $request, $replica)
    {
        //TODO-- Form Validation
        $prefix = '';
        $demoUser = DemoUser::where('username', $replica)->first();

        if (! $demoUser) {
            abort(401);
        }
        $prefix = $demoUser->prefix;
        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::purge('mysql');
        DB::connection('mysql');
        $user = User::with('userDetail')->where('username', $demoUser->username)->first();
        $company = CompanyProfile::first();
        $replicaurl = URL::signedRoute('replica', ['replica' => $replica]);
        $data = $this->getpolicyandTerms($replica);
        $type = 'registration';

        //module_status
        $moduleStatus = $this->moduleStatus();
        $datas = collect([]);
        $datas->put('modulestatus', $moduleStatus);
        $datas->put('terms', TermsAndCondition::first());
        $datas->put('usernameConfig', UsernameConfig::first());

        //product
        if ($moduleStatus['product_status']) {
            $products = Package::ActiveRegPackage()->get();
            $datas->put('products', $products);
            $datas->put('isProductAdded', $this->isProductAdded());
        }

        //countries
        $datas->put('countries', $this->countries($prefix));
        $datas->put('signupSettings', $this->signupSettings($prefix));

        $registerAmount = $this->configuration()['reg_amount'] ?? 0;

        if ($registerAmount || $moduleStatus['product_status']) {
            $paymentGateWay = PaymentGatewayConfig::SortAscOrder()->Registration()->where('slug', '!=', 'e-pin')->where('slug', '!=', 'e-wallet')->get(['name', 'id', 'slug']);
            $datas->put('paymentGateWay', $paymentGateWay);

            if (count($paymentGateWay) == 0) {
                $datas['paymentGateWay']->name = 'bank-trasnfer';
            }
        }

        $signupFields = SignupField::SortAscOrder()->Active()->get();
        if ($signupFields->contains('name', 'country')) {
            $datas = $datas->put('countries', $this->countries());
        }
        $datas->put('customFields', $signupFields);

        return view('replica.register', compact('datas', 'user', 'company', 'replicaurl', 'data', 'registerAmount'));
    }

    public function userRegister(ReplicaUserRegisterRequest $request)
    {
        $prefix = session()->get('prefix');
        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::purge('mysql');
        DB::connection('mysql');

        $requestData = $request->except('_method', '_token');
        $validatedData = $request->validated();
        $pendingData = array_merge($validatedData, $requestData);
        $registerAmount = $this->configuration()['reg_amount'];
        $pendingData['reg_amount'] = $registerAmount;
        $pendingData['totalAmount'] = $registerAmount;
        $moduleStatus = $this->moduleStatus();
        $usernameConfig = UsernameConfig::first();
        $signupSettings = $this->signupSettings($prefix);

        $pendingData['mlm_plan'] = $moduleStatus['mlm_plan'];
        $pendingData['username_type'] = $usernameConfig->user_name_type;
        $pendingData['age_limit'] = $signupSettings['age_limit'];
        $pendingData['default_country'] = $signupSettings['default_country'];
        $mlm_plan = $moduleStatus['mlm_plan'];

        if (! in_array($mlm_plan, ['Binary', 'Matrix'])) {
            //$reg_from_tree = false; TODO
        }

        if ($usernameConfig->user_name_type == 'dynamic') {
            $length = explode(';', $usernameConfig->length);
            $minLength = $length[0];
            $maxLength = $length[1];

            $username = generateUsername($minLength, $maxLength);
            $pendingData['username'] = $username;
        }
        $sponsor_id = User::select('id')->where('username', $request->user)->first();
        $pendingData['sponsor_id'] = $sponsor_id['id'];
        $pendingData['product_amount'] = 0;
        $pendingData['product_pv'] = 0;
        if ($registerAmount > 0 || $this->moduleStatus()['product_status']) {
            $pendingData['totalAmount'] = $registerAmount + Package::find($pendingData['product_id'])->price;
            $pendingData['product_amount'] = Package::find($pendingData['product_id'])->price;
            $pendingData['product_pv'] = Package::find($pendingData['product_id'])->pair_value;

            $paymentTypeId = $request->payment_method;

            $pendingSignupStatus = $this->getPendingSignupStatus($paymentTypeId);
            $paymentMethod = PaymentGatewayConfig::find($request->payment_method);

            $emailVerificationStatus = $this->emailVerificationStatus();

            if ($pendingSignupStatus) {
                $pendingData['date_of_joining'] = date('Y-m-d H:i:s');
                $receipt = '';
                $this->addPendingRegistration($pendingData, $paymentTypeId, $emailVerificationStatus);

                return redirect(route('replica.registerForm', $pendingData['user']))->with('success', 'registration completed successfully. Username is'.' '.$pendingData['username']);
            } else {
                $this->confirmRegister($pendingData);

                return redirect(route('replica.registerForm', $pendingData['user']))->with('success', 'registration completed successfully. Username is'.' '.$pendingData['username']);
            }
        }

        return redirect()->back()->with('success', 'registration_completed_successfully');
    }

    public function addPendingRegistration($pendingData, $paymentTypeId, $emailVerificationStatus)
    {
        $paymentGateWay = PaymentGatewayConfig::findOrfail($paymentTypeId);
        $pendingData = PendingRegistration::create([
            'username' => $pendingData['username'],
            'payment_method' => $paymentGateWay->id,
            'data' => json_encode($pendingData),
            'date_added' => now(),
            'email_verification_status' => $emailVerificationStatus,
            'package_id' => $pendingData['product_id'],
            'sponsor_id' => $pendingData['sponsor_id'],
        ]);
        if ($paymentGateWay->slug == 'bank-transfer') {
            $paymentReceipt = PaymentReceipt::where('username', $pendingData['username'])->first();
            $paymentReceipt->update([
                'pending_registrations_id' => $pendingData->id,
            ]);
        }
    }

    public function addPaymentReceipt(AddBankPaymentReceiptRequest $request)
    {
        $file = $request->file('reciept');
        $prefix = 'bnk_';
        $folder = 'reciept';
        //$model      = $pendingData;
        $uploadreceipt = $this->replicaService->addPaymentReceipt($file, $prefix, $folder, $request->user_name, $request->user);

        return response()->json(['success' => 'Receipt added successfully.']);
    }

    public function confirmRegister($regData)
    {
        try {
            DB::beginTransaction();
            $sponsorId = $regData['sponsor_id'];
            $position = $regData['position'];

            $moduleStatus = $this->moduleStatus();
            $mlmplan = $moduleStatus['mlm_plan'];
            if ($moduleStatus['product_status']) {
                $packageId = $regData['product_id'];
                $productData = Package::find($packageId);
            }

            if ($mlmplan == 'Binary') {
                $placementData = $this->serviceClass->getPlacementData($position, $sponsorId);
            }
            if ($mlmplan == 'Unilevel') {
                $placementData = $this->serviceClass->getUnilevelPlacement($regData['sponsor_id']);
            }

            $fatherData = User::find($placementData->fatherId)->load('ancestors');
            $sponsorData = User::find($regData['sponsor_id'])->load('sponsorAncestors');

            $user = $this->serviceClass->addToUsers($regData, $fatherData, $placementData, $productData, $sponsorData, $mlmplan, '', $moduleStatus['product_status'], $moduleStatus['subscription_status']);
            if (! $user) {
                return redirect()->back()->withErrors('User activation failed');
            }

            $pendingUser = new stdClass();
            $pendingUser->sponsor_id = $sponsorId;
            if ($mlmplan == 'Binary') {
                $this->serviceClass->updatePlacementTable(sponsor: $sponsorData, user: $user, position: $regData['position'], fromTree: $regData['regFromTree'], fatherData: $fatherData);
                    }
            $this->serviceClass->addToUserDetails(compact('pendingUser', 'regData', 'user', 'regData'));

            $this->serviceClass->addToRegistrationDetails(compact('regData', 'productData', 'user'));

            $this->serviceClass->insertTreepath($fatherData, $user);

            $this->serviceClass->createTransPassword($user);
            if(isset($regData['custom'])){
                $this->serviceClass->addToCustomDetails($regData, $user->id);
            }
            if ($mlmplan == 'Binary') {
                $this->serviceClass->addLegDetails($user);
            }

            $this->serviceClass->sponsorTreePath($sponsorData, $user);

            $this->serviceClass->addToUserBalance($user);

            if ($moduleStatus['product_status']) {
                $this->serviceClass->updateGroupPV($user->sponsor, $user->personal_pv, $user->id);
            }

            $paymentStatus = $this->checkPaymentMethod($moduleStatus, $regData, $user, $sponsorData);

            if ($paymentStatus) {
                DB::commit();
            }
        } catch (Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }

    public function checkPaymentMethod($moduleStatus, $regData, $user, $sponsorData)
    {
        $paymentType = PaymentGatewayConfig::find($regData['payment_method']);

        try {
            switch ($paymentType->slug) {
                case 'e-pin':
                    $epin = $this->replicaService->epinPayment($regData, $user);
                    if ($epin) {
                        return true;
                    }
                    break;
                case 'e-wallet':
                    $ewallet = $this->replicaService->ewalletPayment($moduleStatus, $regData, $user, $sponsorData);
                    if ($ewallet) {
                        return true;
                    }
                    break;
                case 'stripe':
                    $serviceClass = new StripeService;
                    $serviceClass->payment($regData, $user);

                    return true;
                    break;
                case 'free-join':
                    return true;
                    break;
                default:
                    // code...
                    break;
            }
        } catch(Throwable $th) {
            DB::rollBack();

            return false;
        }
        // TODO other payment methods
    }

    public function state(Request $request, $username = null)
    {
        $data = $this->replicaService->getuserstate($username);
        $state = view('ajax.userRegister.state', compact('data'));

        return response()->json([
            'status' => true,
            'state' => $state->render(),
        ]);
    }

    public function checkEwalletAvailability(Request $request, $adminusername)
    {
        $prefix = session()->get('prefix');
        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::purge('mysql');
        DB::connection('mysql');

        $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
        ]);
        $username = $request->username;
        $type = '';
        if (auth()->user() != null) {
            $type = auth()->user()->user_type;
        } else {
            $user_type = User::select('user_type')->where('username', $adminusername)->first();
            $type = $user_type['user_type'];
        }

        if ($type == 'admin' || $type == 'employee') {
            $admin = User::where('user_type', 'admin')->with('userBalance', 'transPassword')->first();
            if ($username != $admin->username) {
                return response()->json([
                    'status' => false,
                    'message' => 'invalid credentials',
                ], 422);
            }

            $password = $admin->transPassword->password;
            $passwordCheck = Hash::check($request->password, $password);

            if (! $passwordCheck) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction details not match!',
                ], 422);
            }

            $userBalance = $admin->userBalance->balance_amount;
            $totalRegAmount = $request->totalAmount;
            if ($userBalance >= $totalRegAmount) {
                return response()->json([
                    'status' => true,
                    'message' => 'transaction details is valid',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'insufficient balance',
                ], 422);
            }
        }
    }

    public function checkEpinAvailability(Request $request, $adminusername)
    {
        $prefix = $this->replicaService->getprefix($adminusername);
        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::purge('mysql');
        DB::connection('mysql');

        $request->validate([
            'epinOld.*' => ['sometimes', 'array'],
            'epin' => ['sometimes', 'required_if:remove,true', 'exists:pin_numbers,numbers', function ($attribute, $value, $fail) use ($request) {
                if ($request->epinOld) {
                    $oldEpinValues = collect($request->epinOld)->pluck('value')->all();
                    if (in_array($value, $oldEpinValues)) {
                        $fail(__('epin.epin_already_taken', ['epin' => $value]));
                    }
                }
            }],
            'totalAmount' => 'required|min:1',
            'packageId' => 'sometimes|required|exists:packages,id',
            'sponsor' => 'required',
        ]);
        $moduleStatus = $this->moduleStatus();
        $productPrice = 0;

        $currency = currencySymbol();
        if ($moduleStatus['product_status']) {
            $productPrice = Package::find($request->packageId)->price;
        }
        $regAmount = $this->configuration()['reg_amount'];

        $totalPrice = $regAmount + $productPrice;
        $epin = $request->epin;
        $userId = User::GetAdmin()->id;
        $oldPins = $request->epinOld ?? [];
        $pinNumber = ($epin) ? PinNumber::NonExpired()->AllocateUser($request->sponsorId)
                                            ->where('numbers', $epin)->Active()->first()
                                        : null;
        if (! $pinNumber) {
            throw ValidationException::withMessages([
                'epin' => __('register.epin_cant_use'),
            ]);
        }
        $oldPinsIds = collect($request->epinOld)->pluck('id')->all();

        $totalUsedPinAmount = ($request->epinOld)
                                    ? PinNumber::NonExpired()->AllocateUser($request->sponsorId)->WhereIn('id', [...$oldPinsIds])->get()->sum('balance_amount')
                                    : 0;
        $balancePrice = ($request->epinOld && $request->epinOld != '') ? $totalPrice - $totalUsedPinAmount : $totalPrice;
        if (! $pinNumber) {
            $finalBalance = $balancePrice;
            $newPinBalance = 0;
        } elseif ($balancePrice > 0 && $balancePrice > $pinNumber->balance_amount) {
            $finalBalance = $balancePrice - $pinNumber->balance_amount;
            $newPinBalance = 0;
        } elseif ($balancePrice > 0 && $balancePrice < $pinNumber->balance_amount) {
            $newPinBalance = $pinNumber->balance_amount - $balancePrice;
            $finalBalance = 0;
        } else {
            $finalBalance = 0;
            $newPinBalance = 0;
        }
        $usedPinAmount = ($pinNumber) ? $pinNumber->balance_amount - $newPinBalance : 0;

        $billStatus = $finalBalance == 0 ? true : false;
        $EpinTotal = $usedPinAmount + $totalUsedPinAmount;

        $totalEpinAmount = ($billStatus) ? $request->totalAmount : round($EpinTotal, 8);

        $view = view('ajax.userRegister.epin', compact('pinNumber', 'billStatus', 'oldPins', 'totalEpinAmount', 'finalBalance', 'currency', 'usedPinAmount'));

        return response()->json([
            'status' => true,
            'view' => $view->render(),
            'finishStatus' => $billStatus,
        ]);
    }

    public function checkLegAvailability($sponsorLeg, $sponsorUserName)
    {
        $sponsor = User::where('username', $sponsorUserName)->first();

        if (! isset($sponsor)) {
            return 'no';
        }
        $adminLockedBinaryLeg = $this->getSignupBinaryLeg();
        if ($adminLockedBinaryLeg != 'any') {
            if ($sponsor->user_type == 'admin') {
                if ($adminLockedBinaryLeg != $sponsorLeg) {
                    return 'no';
                } else {
                    $adminId = User::GetAdmin()['id'];
                    $adminLegs = $this->getUserLeftRightNode($adminId);
                    $adminLegId = $adminLegs[$adminLockedBinaryLeg];
                }
                // TODO check Ancestor function in treepath in a if condition
            }

            return 'yes';
        }

        if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'employee') {
            return 'yes';
        }

        // TODO check Leg is availavble
        //  References Register.php checkSponsorLegAvailable()
    }

    public function getState(Request $request, $country, $username)
    {
        $data = $this->replicaService->getstate($request, $country, $username);
        $state = view('ajax.userRegister.specifiedState', compact('data'));

        return response()->json([
            'status' => true,
            'state' => $state->render(),
        ]);
    }

    public function checkDob(Request $request)
    {
        $request->validate(['dob' => 'required']);
        if ($request->has('dob') && $request->dob != '') {
            $ageLimit = '18';
            //$settings   = SignupSetting::first();
            if ($ageLimit) {
                $ageLimit = Carbon::now()->subYears(18);
                $dob = Carbon::parse($request->dob);
                if ($dob->lte($ageLimit)) {
                    return response()->json([
                        'status' => true,
                    ]);
                }
                throw ValidationException::withMessages([
                    'dob' => __('register.age_should_be_atleast_years_old', ['age' => 18]),
                ]);
            }

            return response()->json([
                'status' => true,
            ]);
        }
    }

    public function checkPackage(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);
    }

    public function checkMobile(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'mobile' => 'required|numeric',
        ]);
    }

    public function checkUsername(Request $request)
    {
        $prefix = $this->replicaService->getprefix('binaryaddon');
        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::purge('mysql');
        DB::connection('mysql');
        $usernameConfig = UsernameConfig::first()->length;
        $usernameLength = Str::of($usernameConfig)->explode(';');
        $passwordPolicy = PasswordPolicy::first();
        $passwordRules = [];
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
            'username' => 'required|between:'.$usernameLength[0].','.$usernameLength[1],
            'password' => ['required', ...$passwordRules],
            'confirmPassword' => 'required|same:password',
            'terms' => 'required|accepted',
        ]);
        $availabe = true;
        $checkPeding = PendingRegistration::where('username', $request->username)->exists();
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
}
