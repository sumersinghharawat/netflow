<?php

namespace App\Http\Controllers\Ajax;

use App\Models\User;
use App\Models\State;
use App\Models\Country;
use App\Models\Package;
use App\Models\Treepath;
use App\Models\PinNumber;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Models\PaymentReceipt;
use App\Models\UsernameConfig;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CoreInfController;
use Illuminate\Validation\ValidationException;

class UserRegisterController extends CoreInfController
{
    public function sponsorUsername(Request $request)
    {
        if (!$request->has('placement') || !$request->has('sponsor')) {
            throw ValidationException::withMessages([
                'sponsorName' => __('register.sponsor_is_required'),
            ]);
        }

        $placement = $request->placement;
        $username = $request->sponsor;
        $placement = User::where('username', $placement)->first();
        $sponsor = User::where('username', $username)->where('active', 1)->with('userDetail')->first();
        if (!$sponsor) {
            throw ValidationException::withMessages([
                'sponsorName' => __('register.sponsor_not_valid'),
            ]);
        }
        if (!$placement) {
            throw ValidationException::withMessages([
                'sponsorName' => __('register.placement_not_valid'),
            ]);
        }
        $checkAncestor = Treepath::where('descendant', $placement->id)->where('ancestor', $sponsor->id)->exists();
        if (!$checkAncestor && $request->regfrom_tree == '1') {
            throw ValidationException::withMessages([
                'sponsorName' => __('register.sponsor_should_be_ancestor_of_placement'),
            ]);
        }
        return response()->json([
            'status' => true,
            'data' => $sponsor,
        ]);
    }

    public function replicaSponsorName(Request $request, $sponsor)
    {
        if (!$request->has('sponsor')) {
            throw ValidationException::withMessages([
                'sponsorName' => __('register.sponsor_is_required'),
            ]);
        }
        $username = $request->sponsor;
        $sponsor = User::where('username', $username)->where('active', 1)->with('userDetail')->first();
        if (!$sponsor) {
            throw ValidationException::withMessages([
                'sponsorName' => __('register.sponsor_not_valid'),
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $sponsor,
        ]);
    }

    public function state()
    {
        if (auth()->user()->user_type == 'admin') {
            $country_id = auth()->user()->userDetail->country_id;
        } else {
            $country_id = auth()->user()->employeeDetail->country_id;
        }
        $data = [
            'state' => State::orderBy('name', 'ASC')->where('country_id', $country_id)->get(),
            'status' => $this->stateStatus(),
        ];
        $state = view('ajax.userRegister.state', compact('data'));

        return response()->json([
            'status' => true,
            'state' => $state->render(),
        ]);
    }

    public function getState(Request $request, $country)
    {
        $country = Country::with('states')->find($country);
        $stateId = 0;
        if ($request->has('userId')) {
            $stateId = UserDetail::where('user_id', $request->userId)->first()->state_id;
        }
        $data = [
            'state' => $country->states,
            'status' => $this->stateStatus(),
        ];
        $state = view('ajax.userRegister.specifiedState', compact('data', 'stateId'));
        return response()->json([
            'status' => true,
            'state' => $state->render(),
        ]);
    }

    public function totalAmount($id)
    {
        $package = Package::find($id);
        $registerAmount = $this->configuration()['reg_amount'];

        $totalAmount = round($package->price) + $registerAmount;

        return response()->json([
            'status' => true,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function checkLegAvailability($leg, $sponsor)
    {
        // TODO uncomment when userwise leg block is completed
        return true;
        $admin = User::GetAdmin();
        $sponsor = User::where('username', $sponsor)->first();
        if (!$sponsor) {
            throw ValidationException::withMessages([
                'sponsor' => __('register.sponsor_not_available'),
            ]);
        }
        $binaryLeg = $this->signupSettings()->binary_leg;
        if ($binaryLeg == 'left') {
            $adminLockedBinaryLeg = 'L';
        } elseif ($binaryLeg == 'right') {
            $adminLockedBinaryLeg = 'R';
        } else {
            $adminLockedBinaryLeg = 'any';
        }
        if ($adminLockedBinaryLeg != 'any') {
            if ($sponsor->user_type == 'admin') {
                if ($adminLockedBinaryLeg != $leg) {
                    throw ValidationException::withMessages([
                        'position' => __('register.position_not_available'),
                    ]);
                }
            } else {
                $adminLegs = [
                    'L' => '',
                    'R' => '',
                ];
                $data = User::fatherId($admin->id)->get(['id', 'position']);
                foreach ($data as $leg) {
                    if ($leg['position'] == 'L') {
                        $adminLegs['L'] = $leg['id'];
                    } else {
                        $adminLegs['R'] = $leg['id'];
                    }
                }
                $adminLegId = $adminLegs[$adminLockedBinaryLeg];
                $count = Treepath::where('ancestor', $adminLegId)
                    ->where('descendant', $sponsor->id)
                    ->count();
                if (!$count) {
                    throw ValidationException::withMessages([
                        'position' => __('register.position_not_available'),
                    ]);
                }
            }
        }
        if ((auth()->check() && auth()->user()->user_type == 'admin') || auth()->guard('employee')->check()) {
            return 'yes';
        }
        // TODO Userwise binary block
    }

    public function checkEpinAvailability(Request $request)
    {
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
        if (!$pinNumber) {
            throw ValidationException::withMessages([
                'epin' => __('register.epin_cant_use'),
            ]);
        }
        if (!$pinNumber) {
            throw ValidationException::withMessages([
                'epin' => __('register.epin_cant_use'),
            ]);
        }
        $oldPinsIds = collect($request->epinOld)->pluck('id')->all();

        $totalUsedPinAmount = ($request->epinOld)
            ? PinNumber::NonExpired()->AllocateUser($request->sponsorId)->WhereIn('id', [...$oldPinsIds])->get()->sum('balance_amount')
            : 0;
        $balancePrice = ($request->epinOld && $request->epinOld != '') ? $totalPrice - $totalUsedPinAmount : $totalPrice;
        if (!$pinNumber) {
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

    public function checkEwalletAvailability(Request $request)
    {
        $request->validate([
            'transaction_username' => 'required|exists:users,username',
            'tranPassword' => 'required',
            'sponsor' => 'required',
        ]);
        $username = $request->transaction_username;
        $requestUser = User::where('user_type', '!=', 'employee')->where('username', $request->transaction_username)->with('userBalance', 'transPassword')->first();

        $sponsorData = User::where('user_type', '!=', 'employee')->where('username', $request->sponsor)->with('userBalance', 'transPassword')->first();

        if ($sponsorData->username != $username) {
            throw ValidationException::withMessages([
                'tranPassword' => __('register.username_not_correct'),
            ]);
        }

        $password = $requestUser->transPassword->password;
        $passwordCheck = Hash::check($request->tranPassword, $password);

        if (!$passwordCheck) {
            throw ValidationException::withMessages([
                'tranPassword' => __('register.transaction_password_not_correct'),
            ]);
        }

        $userBalance = $requestUser->userBalance->balance_amount;
        $totalRegAmount = defaultCurrency($request->totalAmount);
        if ($userBalance < $totalRegAmount) {
            return response()->json([
                'status' => false,
                'message' => __('register.insuficient_wallet_balance'),
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'transaction details is valid',
        ]);
    }

    public function removeBankReciept(Request $request)
    {
        try {
            $receipt = PaymentReceipt::where([['username', $request->username], ['type', 'register']])->first();
            Storage::delete('reciept/' . $receipt->receipt);
            $receipt->delete();

            return response()->json([
                'status' => true,
                'message' => trans('register.receipt_remove_message'),
            ]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function generateDynamicUsername()
    {
        try {
            $usernameConfig = UsernameConfig::first();
            if ($usernameConfig->user_name_type == 'dynamic') {
                $length = explode(';', $usernameConfig->length);
                $minLength = $length[0];
                $maxLength = $length[1];
                $username = generateUsername($minLength, $maxLength);

                return response()->json([
                    'status' => true,
                    'username' => $username,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}
