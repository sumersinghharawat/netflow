<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdditionalDetailsUpdateRequest;
use App\Http\Requests\NowpaymentDetailRequest;
use Throwable;
use App\Models\User;
use App\Models\State;
use App\Models\UserDetail;
use App\Models\SignupField;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use App\Services\SendMailService;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentGatewayDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserBlockActiveHistory;
use App\Http\Requests\PaypalDetailRequest;
use App\Http\Requests\StripeDetailRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\RequestsProfileImageUpdate;
use App\Http\Requests\RequesttransPasswordUpdate;
use App\Http\Requests\RequestcontactDetailsUpdate;
use App\Http\Requests\RequestsProfileDetailUpdate;
use App\Http\Requests\RequestsProfileBankDetailUpdate;
use App\Http\Requests\updatePvRequest;
use App\Http\Requests\userPaymentDetailsUpdateRequest;
use App\Jobs\UserActivityJob;
use App\Models\CustomfieldValues;
use App\Models\ManualPvUpdateHistory;
use App\Services\commissionService;
use App\Services\UserApproveService;

class ProfileDetailsController extends CoreInfController
{
    use UploadTraits;

    public function index(Request $request)
    {
        $mouleStatus    = $this->moduleStatus();
        $signupField    = SignupField::MandatoryFields()->Active()->get();
        $customFields   = SignupField::ActiveCustom()->with('customFieldLang')->get();
        if ($request->username) {
            $user = User::with('rankDetail', 'package', 'userDetail.payout', 'additionalDetails')->FindOrFail($request->username);
            $upgradablePack = ($mouleStatus->package_upgrade)
                ? $user->checkPackageUpgradeAvailable()
                : false;
            $currentDate = date('Y-m-d H:i:s');
            if (auth()->user()->user_type == 'admin') {
                $country_id = auth()->user()->userDetail->country_id;
            } else {
                $country_id = auth()->user()->employeeDetail->country_id;
            }
            $data = [
                'countries'         => $this->countries(),
                'state'             => State::NameAscorder()->where('country_id', $country_id)->get(),
                'status'            => $this->stateStatus(),
                'moduleStatus'      => $mouleStatus,
                'user'              => $user,
                'upgradablePack'    => $upgradablePack,
            ];
        } else {
            if (auth()->user()->user_type == 'employee') {
                $user = User::with('rankDetail', 'package', 'userDetail', 'additionalDetails')->GetAdmin();
                $country_id = auth()->user()->employeeDetail->country_id;
                $upgradablePack = [];
            } else {
                $user = auth()->user()->load('rankDetail', 'package', 'userDetail', 'additionalDetails');
                $country_id = auth()->user()->userDetail->country_id;
                $upgradablePack = ($mouleStatus->package_upgrade)
                    ? $user->checkPackageUpgradeAvailable()
                    : false;
            }
            $currentDate = date('Y-m-d H:i:s');
            $data = [
                'countries' => $this->countries(),
                'state' => State::NameAscorder()->where('country_id', $country_id)->get(),
                'status' => $this->stateStatus(),
                'moduleStatus' => $this->moduleStatus(),
                'user' => $user,
                'upgradablePack' => $upgradablePack,
            ];
        }
        $paymentGatewayDetail   =  PaymentGatewayConfig::select('name', 'slug', 'id')->where('payout_status', true)->get();
        $data['paymentGateway'] = $paymentGatewayDetail;
        return view('admin.profile.profile_view', compact('data', 'currentDate', 'signupField', 'customFields'));
    }

    public function profileUpdate(RequestsProfileImageUpdate $request)
    {
        // if(session()->get('is_preset')){
        //     return response()->json([
        //         'status' => 'error',
        //         'errors' => "You don't have permission By using Preset Demo",
        //     ], 401);
        // }

        try {
            if ($request->userId) {
                $user = User::find($request->userId);
            } else {
                $user = User::find(auth()->user()->id);
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $model = $user->userDetail;

                if (!$this->singleFileUpload($file, $model, 'user-', 'profile')) {
                    DB::rollback();
                    if ($request->ajax()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'file upload failed.',
                        ], 400);
                    }

                    return redirect(route('profile.view'))->with('error', 'file upload failed.');
                }
            }

            if ($request->ajax()) {
                $image = $user->userDetail->image;
                return response()->json([
                    'status' => true,
                    'message' => 'profile photo updated successfully',
                    'image' => $image,
                ], 200);
            }
            return redirect()->back()->with('success', 'profile photo updated successfully');
        } catch (\Throwable $th) {
            return redirect(route('profile.view'))
                ->with('error', $th->getMessage());
        }
    }

    public function transPasswordUpdate(RequesttransPasswordUpdate $request)
    {
        if (session()->get('is_preset')) {
            // return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
            return response()->json([
                'status' => 'error',
                'msg' => "You don't have permission By using Preset Demo",
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        try {
            if ($request->userId) {
                $user = User::find($request->userId);
            } else {
                $user = auth()->user();
            }

            $transactionPassword = $user->transPassword()->first();
            if ($user->user_type == 'admin') {
                if (!Hash::check($request->current_password, $transactionPassword->password)) {
                    throw ValidationException::withMessages([
                        'current_password' => 'The Current Password Is Incorrect ',
                    ]);
                }
            }
            $transactionPassword->password = Hash::make($request->password);
            $transactionPassword->push();

            //mail
            $userDetail = UserDetail::where('user_id', $request->userId)->first();

            // $sendDetails['email'] = $userDetail->email;
            $sendDetails['email'] = $user->email;
            $sendDetails['first_name'] = $userDetail->name;
            $sendDetails['last_name'] = $userDetail->second_name ?? '';
            $sendDetails['tranpass'] = $request->password;

            $serviceClass = new SendMailService;
            $serviceClass->sendAllEmails("send_tranpass", $user, $sendDetails);

            return response()->json([
                'status'  => true,
                'message' => 'Transaction Password Updated successfully',
            ], 200);
        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Error',
                'errors' => $exception->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Error',
                'errors' => $th->getMessage(),
            ], 400);
        }
    }

    public function profileDetailsUpdate(RequestsProfileDetailUpdate $request)
    {
        try {
            if ($request->userId) {
                $user = User::with('userDetail')->find($request->userId);
            } else {
                $user = Auth::user()->load('userDetail');
            }

            $data = [
                'name' => $request->firstname,
                'second_name' => $request->lastname,
                'gender' => $request->gender,
                'dob' => $request->dob,
            ];

            $user->userDetail()->update($data);

            return response()->json([
                'message' => 'profile details updated successfully',
            ]);
        } catch (\Exception $e) {
            return redirect(route('profile.view'))
                ->with('error', $e->getMessage());
        }
    }

    public function bankDetailsUpdate(RequestsProfileBankDetailUpdate $request)
    {
        try {
            if ($request->userId) {
                $user = User::with('userDetail')->find($request->userId);
            } else {
                $user = Auth::user()->load('userDetail');
            }

            $data = [
                'bank' => $request->bank_name,
                'branch' => $request->branch_name,
                'nacct_holder' => $request->acc_holder,
                'account_number' => $request->acc_number,
                'ifsc' => $request->ifsc,
                'pan' => $request->pan,
            ];

            $user->userDetail()->update($data);

            return response()->json([
                'message' => 'bank details updated successfully',
            ]);
        } catch (\Exception $e) {
            return redirect(route('profile.view'))
                ->with('error', $e->getMessage());
        }
    }

    public function stripeDetailsUpdate(StripeDetailRequest $request, $id)
    {
        if (session()->get('is_preset')) {
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        try {
            DB::beginTransaction();

            $paymentGatewayConfig = PaymentGatewayConfig::find($id);
            if (!$paymentGatewayConfig) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Gateway not found',
                ]);
            }

            $paymentGatewayConfig->details()->updateOrCreate([
                'payment_gateway_id' => $id,
            ], [
                'public_key' => $request->public_key,
                'secret_key' => $request->secret_key,
            ]);
            $paymentGatewayConfig->mode = $request->mode;
            $paymentGatewayConfig->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment Gateway updated successfully',
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    public function nowpaymentDetailsUpdate(NowpaymentDetailRequest $request, $id)
    {
        if (session()->get('is_preset')) {
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        try {
            DB::beginTransaction();

            $paymentGatewayConfig = PaymentGatewayConfig::find($id);
            if (!$paymentGatewayConfig) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Gateway not found',
                ]);
            }

            $paymentGatewayConfig->details()->updateOrCreate([
                'payment_gateway_id' => $id,
            ], [
                'public_key' => $request->public_key,
            ]);
            $paymentGatewayConfig->mode = $request->mode;
            $paymentGatewayConfig->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment Gateway updated successfully',
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    public function paypalDetailsUpdate(PaypalDetailRequest $request)
    {
        if (session()->get('is_preset')) {
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        try {
            DB::beginTransaction();
            $paymentGatewayDetail = PaymentGatewayDetail::where('payment_gateway_id', $request->id)->first();
            if (!$paymentGatewayDetail) {
                $PaymentGateway = new PaymentGatewayDetail();
                $PaymentGateway->create([
                    'payment_gateway_id' => $request->id,
                    'public_key' => $request->public_key,
                    'secret_key' => $request->secret_key,
                ]);

                $paymentGatewayConfig = PaymentGatewayConfig::find($request->id);
                $paymentGatewayConfig->update([
                    'mode' => $request->mode,
                ]);
            } else {
                $paymentGatewayDetail->update([
                    'payment_gateway_id' => $paymentGatewayDetail->payment_gateway_id,
                    'public_key' => $request->public_key,
                    'secret_key' => $request->secret_key,
                ]);
                $paymentGatewayConfig = PaymentGatewayConfig::find($request->id);
                $paymentGatewayConfig->update([
                    'mode' => $request->mode,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment Gateway updated successfully',
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    public function contactDetailsUpdate(RequestcontactDetailsUpdate $request)
    {
        try {
            if ($request->userId) {
                $user = User::with('userDetail')->find($request->userId);
                $userId = $request->userId;
            } else {
                $user = Auth::user()->load('userDetail');
            }

            $data = [
                'address' => $request->address,
                'address2' => $request->address2,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city' => $request->city,
                'pin' => $request->pin,
                // 'email' => $request->email,
                'mobile' => $request->mob,
                'land_phone' => $request->phone,
            ];
            $email = $request->email;
            $user->userDetail()->update($data);
            User::where('id', $userId)->update(['email' => $email]);


            return response()->json([
                'message' => 'contact details updated successfully',
            ]);
        } catch (\Exception $e) {
            return redirect(route('profile.view'))
                ->with('error', $e->getMessage());
        }
    }

    public function searchUser()
    {
        return back()
            ->with('sucess', 'User not found');
    }

    public function ChangeUserActiveStatus(Request $request)
    {
        if (session()->get('is_preset')) {
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        DB::beginTransaction();
        $validated = $request->validate([
            'user' => 'required',
        ]);
        try {
            $user = User::findOrFail($validated['user']);
            $user->active = ($user->active) ? 0 : 1;
            $user->save();
            $history = new UserBlockActiveHistory();
            $history->user_id = $validated['user'];
            $history->type = 'admin';
            $history->action = ($user->active) ? 'Activated' : 'Blocked';
            $history->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        DB::commit();

        return response()->json([
            'status' => true,
            'data' => ($user->active) ? 'Active' : 'Blocked',
        ]);
    }

    public function updatePaymentDetails(userPaymentDetailsUpdateRequest $request, $userId)
    {
        if (session()->get('is_preset')) {
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        try {
            $payoutType = PaymentGatewayConfig::where('slug', 'bank-transfer')->first()->id;
            $userDetail = UserDetail::where('user_id', $userId)->first();
            $userDetail->paypal = base64_encode($request->paypal) ?? 'NA';
            $userDetail->stripe = base64_encode($request->stripe) ?? 'NA';
            $userDetail->payout_type =  $request->payout_type ?? $payoutType;
            $userDetail->push();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => trans('profile.payment_details_update_success')
                ]);
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function updateDefaultSettings(Request $request, $id)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $user = User::findOrFail($id);
            $moduleStatus = $this->moduleStatus();
            if ($moduleStatus->multilang_status) {
                $user->default_lang = $request->default_lang;
            }

            if ($moduleStatus->multi_currency_status) {
                $user->default_currency = $request->default_currency;
            }

            if ($user->user_type == 'user' && $moduleStatus->mlm_plan == 'Binary') {
                $user->binary_leg = $request->binary_position;
            }

            if ($moduleStatus->google_auth_status) {
                $user->google_auth_status = $request->google_auth_status;
            }

            $user->push();

            return redirect()->back()->with('success', trans('profile.settings_success'));
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    public function additionalDetailUpdate(AdditionalDetailsUpdateRequest $request)
    {
        if (session()->get('is_preset')) {
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        try {
            $validatedData = $request->validated();
            $fields = [];
            foreach ($validatedData['required'] as $key => $item) {
                $fields[$key] = $item;
            }
            foreach ($validatedData['non_required'] as $key => $item) {
                $fields[$key] = $item;
            }
            foreach ($fields as $key => $item) {
                CustomfieldValues::updateOrCreate([
                    'customfield_id' => $key,
                    'user_id' => $request->userID
                ], [
                    'value' => $item
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Additional details updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updatePv(updatePvRequest $request, $id)
    {
    try {
        if (session()->get('is_preset')) {
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        DB::beginTransaction();
            $user = User::with('sponsor')->find($id);
            $oldPv = $user->personal_pv ?? 0;
            $newpv = $request->pv;
            $action = $request->action;
            if ($action == 'add') {
                $totalpv = (int)$oldPv + (int)$newpv;
            } elseif ($action == 'deduct') {
                $totalpv = (int) $oldPv - (int)$newpv;
            }
            $moduleStatus = $this->moduleStatus();
            $userApproveService = new UserApproveService;
            $pvType = $request->action == 'add' ? 'pv_added' : 'pv_deducted';
            $user->personal_pv = $totalpv;
            $user->push();

            $userApproveService->insertPVhistoryDetails($id, $newpv, 'personal_pv', $id, 'manualpv_add_by_admin');
            if ($action == 'deduct') {
                $this->deductGroupPV($user->sponsor, $newpv, $id);
            } else {
                $userApproveService->updateGroupPV($user->sponsor, $newpv, $id, 'manualpv_add_by_admin');
            }
            ManualPvUpdateHistory::create([
                'user_id' => $id,
                'new_pv' => $totalpv,
                'pv_added' => $newpv,
                'old_pv' => $oldPv,
                'type' => $pvType,
            ]);
            DB::commit();

            if ($moduleStatus->rank_status) {
                $commission = new commissionService;
                $prefix = session()->get('prefix');
                $commission->updateUplineRank($user->id, $prefix);
            }

            $prefix = session()->get('prefix');
            UserActivityJob::dispatch(
                auth()->user()->id,
                [],
                $pvType,
                auth()->user()->username . ' ' . $pvType . ' ' . $newpv . ' manually ',
                $prefix . '_',
                auth()->user()->user_type
            );
            return response()->json([
                'status' => true,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
        }
    }

    public function deductGroupPV($user, $pv, $userId, $action = 'manualpv_add_by_admin')
    {
        if ($user) {
            $user->load('sponsor');
            $user->update([
                'group_pv' => $user->group_pv - $pv,
            ]);
            if ($user->sponsor != null) {
                $this->deductGroupPV($user->sponsor, $pv, $userId);
            }
            $service = new UserApproveService;
            $service->insertPVhistoryDetails($user->id, $pv, 'group_pv', $userId, $action);
        }
        return true;
    }
}
