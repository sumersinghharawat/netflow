<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddEpinRequest;
use App\Http\Requests\allocatePendingEpinRequest;
use App\Http\Requests\EpinPurchaseRequest;
use App\Http\Requests\EpinTransferRequest;
use App\Models\EpinTransferHistory;
use App\Models\PinAmountDetails;
use App\Models\PinNumber;
use App\Models\PinRequest;
use App\Models\User;
use App\Models\UserBalanceAmount;
use App\Rules\EpinUserTransferRule;
use App\Services\EwalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Notification;
use App\Notifications\EpinApprovedNotification;

class EpinController extends CoreInfController
{
    public function index()
    {
        $moduleStatus = $this->moduleStatus();

        if (!$moduleStatus->pin_status) {
            return redirect()->back()->withErrors(trans('common.not_configured'));
        }
        // $activePinNumbers       =       PinNumber::with('allocatedUser.userDetail')->latest()->NonExpired()->paginate(10);
        $balanceAmount = PinNumber::Active()->NonExpired()->sum('balance_amount');
        $activePins = PinNumber::Active()->NonExpired()->count();
        $amounts = PinAmountDetails::AscOrder()->get();
        $requestedPins = PinRequest::with('requestedUser.userDetail')->Active()->latest()->paginate(10);
        $pendingPins = $requestedPins->total();
        $currency = currencySymbol();

        return view('admin.epin.index', compact('amounts', 'activePins', 'balanceAmount', 'pendingPins', 'requestedPins', 'currency'));
    }

    public function activeEpins(Request $request)
    {
        $activePinNumbers = PinNumber::with('allocatedUser.userDetail')->latest()->NonExpired();
        $currency = currencySymbol();

        if ($request->has('status')) {
            $activePinNumbers->where('status', $request->status);
        }
        if ($request->has('username') && $request->username != null) {
            $activePinNumbers->whereIn('allocated_user', [...$request->username]);
        }
        if ($request->has('epin') && $request->epin != null) {
            $activePinNumbers->whereIn('id', [...$request->epin]);
        }
        if ($request->has('amount') && $request->amount != null) {
            $activePinNumbers->whereIn('amount', [...$request->amount]);
        }

        return DataTables::of($activePinNumbers)
            ->addColumn('checkbox', function ($data) {
                $onclickAttribute = ($data->status !== 'deleted') ? "onclick='showEpinActiveActionPopup()'" : "";
                $disabled = ($data->status === 'deleted') ? "disabled" : "";
                return "<div class='form-group'>
                    <input type='checkbox' class='form-check-input epin-active-single' {$disabled} id='{$data->id}' value='{$data->id}' {$onclickAttribute}>
                </div>";
            })
            ->addColumn('username', function ($data) {
                return '<div class="d-flex"><img class="rounded-circle avatar-md" src="' . asset('/assets/images/users/avatar-1.jpg') . '"><div class="transaction-user"><h5>' . $data->allocatedUser->userDetail->name . '</h5><span>' . $data->allocatedUser->username . '</span></div></div';
            })
            ->editColumn('amount', function ($data) use ($currency) {
                return '<span
                class="badge badge-pill badge-soft-primary font-size-12">' . $currency . ' ' . formatCurrency($data->amount) . '</span>';
            })
            ->editColumn('balance_amount', function ($data) use ($currency) {
                return '<span
                class="badge badge-pill badge-soft-success font-size-12">' . $currency . ' ' . formatCurrency($data->balance_amount) . '</span>';
            })
            ->editColumn('status', fn ($data) => '<span class="text-success">' . $data->status . '</span>')
            ->editColumn('expiry', fn ($data) => date('F j, Y', strtotime($data->expiry_date)))
            ->rawColumns(['username', 'amount', 'balance_amount', 'status', 'expiry', 'checkbox'])
            ->make(true);
    }

    public function PendingRequests(Request $request)
    {
        $requestedPins = PinRequest::with('requestedUser.userDetail')->Active()->latest();
        $currency = currencySymbol();

        if ($request->has('username') && $request->username != null) {
            $requestedPins->whereIn('user_id', [...$request->username]);
        }

        return DataTables::of($requestedPins)
            ->addColumn('checkbox', function ($data) {
                return "<div class='form-group'>
                        <input type='checkbox' class='form-check-input epin-check-single' id='{$data->id}' value='{$data->id}' onclick='showEpinRequestsActionPopup()'>
                    </div>";
            })
            ->addColumn('username', function ($data) {
                return  '<div class="d-flex"><img class="rounded-circle avatar-md" src="' . asset('/assets/images/users/avatar-1.jpg') . '"><div class="transaction-user"><h5>' . $data->requestedUser->userDetail->name . '</h5><span>' . $data->requestedUser->username . '</span></div></div';
            })
            ->addColumn('allotted_pin', function ($data) {
                $options = '';
                for ($i = 1; $i <= $data->requested_pin_count; $i++) {
                    $selected = $i == $data->requested_pin_count ? 'selected' : '';
                    $options .= "<option value='{$i}' $selected>{$i}</option>";
                }
                return "<select name='count[]' class='form-control' id=''>$options</select>";
                //     return ' <input type="numer" name="count[]" class="w-50" min="0"
                // id="requested_count' . $data->id . '"
                // max="' . $data->requested_pin_count . '"
                // value="' . $data->requested_pin_count . '">';
            })
            ->editColumn('pin_amount', function ($data) use ($currency) {
                return $currency . ' ' . formatCurrency($data->pin_amount);
            })
            ->editColumn('requested_pin_count', fn ($data) => '<span id="allocate' . $data->id . '">' . $data->requested_pin_count . '</span>')
            ->editColumn('requested_date', fn ($data) => date('F j, Y', strtotime($data->requested_date)))
            ->editColumn('expiry_date', fn ($data) => date('F j, Y', strtotime($data->expiry_date)))
            ->rawColumns(['username', 'allotted_pin', 'pin_amount', 'requested_date', 'expiry_date', 'requested_pin_count', 'checkbox'])
            ->make(true);
    }

    public function store(AddEpinRequest $request)
    {
        DB::beginTransaction();
        if ($request->count > 50) {
            return response()->json([
                'status' => false,
                'message' => 'Pin count is not valid',
            ], 403);
        }
        try {
            $count = $request->count;
            $amount = $this->getEpinAmount($request->amount);
            $user_id = $request->username;
            for ($i = 1; $i <= $count; $i++) {
                $pinNumber = generatePinNumber();

                $epin = PinNumber::create([
                    'numbers' => $pinNumber,
                    'alloc_date' => now(),
                    'generated_user' => auth()->user()->id,
                    'allocated_user' => $user_id,
                    'uploaded_date' => now(),
                    'expiry_date' => $request->expiry,
                    'amount' => $amount,
                    'balance_amount' => $amount,
                ]);
            }
            DB::commit();
            $this->sendEpinApprovedNotification($user_id, $count, $amount, '');
            if ($request->ajax()) {
                $balanceAmount = PinNumber::Active()->NonExpired()->sum('balance_amount');
                $activePins = PinNumber::with('allocatedUser.userDetail')->latest()->Active()->NonExpired()->count();

                return response()->json([
                    'status' => true,
                    'message' => 'pin allocated succesfully',
                    'balanceAmount' => number_format($balanceAmount, 2),
                    'activePins' => $activePins,
                ], 200);
            } else {
                return redirect(route('epin.index'))->with('success', trans('epin.pinAllocatedMsg'));
            }
        } catch (Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 404);
            } else {
                return redirect(route('epin.index'))->with('error', $th->getMessage());
            }
        }
    }

    public function purchaseStore(EpinPurchaseRequest $request)
    {
        DB::beginTransaction();
        try {
            $username = $request->username;
            $pinCount = $request->purchase_count;
            $amount_id = $request->purchase_amount;
            $balanceAmounts = $this->getBalanceAmount($username);
            $balanceAmount = $balanceAmounts->balance_amount;
            $currency = currencySymbol();
            $ewalletService = new EwalletService;
            $moduleStatus = $this->moduleStatus();
            if ($pinCount > 0 && $amount_id != ' ' && is_numeric($pinCount)) {
                $amount = $this->getEpinAmount($amount_id);
                $tot_avb_amt = $amount * $pinCount;
                if ($tot_avb_amt <= $balanceAmount) {
                    $max_active_pincount = $this->getEpinConfig()['max_count'];
                    $current_active_pin_count = PinNumber::NonExpired()->ActivePurchaseStatus()->count();
                    $generatedUser = (auth()->user()->user_type == 'employee' ? User::GetAdmin() : auth()->user());
                    if ($current_active_pin_count < $max_active_pincount) {
                        $balance_count = $max_active_pincount - $current_active_pin_count;
                        if ($pinCount <= $balance_count) {
                            for ($i = 1; $i <= $pinCount; $i++) {
                                $transactionId = generateTransactionNumber(9);
                                $pinNumber = generatePinNumber();

                                $epin = PinNumber::create([
                                    'numbers' => $pinNumber,
                                    'alloc_date' => now(),
                                    'generated_user' => $generatedUser->id,
                                    'allocated_user' => $username,
                                    'uploaded_date' => now(),
                                    'expiry_date' => $request->purchase_expiry,
                                    'amount' => $amount,
                                    'balance_amount' => $amount,
                                    'transaction_id' => $transactionId,
                                    'purchase_status' => 1,
                                ]);
                                $refernceId = $epin->id;

                                $userWalletBalance = UserBalanceAmount::select('balance_amount')->whereKey($username)->first()->balance_amount;

                                $ewalletService->addToEwalletPurchaseHistory($moduleStatus, $balanceAmounts->user, $refernceId, 'pin_purchase', $amount, $amount, 'pin_purchase', 'pin_purchase');

                                UserBalanceAmount::whereKey($username)->update([
                                    'balance_amount' => $userWalletBalance - $amount,
                                ]);
                            }
                            DB::commit();

                            if ($request->ajax()) {
                                $activePinNumbers = PinNumber::with('allocatedUser.userDetail')->latest()->Active()->NonExpired()->paginate(10);
                                $balanceAmount = PinNumber::Active()->NonExpired()->sum('balance_amount');
                                $activePins = $activePinNumbers->total();

                                $view = view('admin.epin.ajax.activeEpins', compact('activePinNumbers', 'currency'));

                                return response()->json([
                                    'status' => true,
                                    'message' => trans('epin.epinPurchaseSuccess'),
                                    'data' => $view->render(),
                                    'balanceAmount' => formatCurrency($balanceAmount),
                                    'activePins' => $activePins,
                                ], 200);
                            }
                        } else {
                            DB::rollBack();
                            throw ValidationException::withMessages([
                                'purchase_count' => __('epin.pincount_lessthan_balance_count'),
                            ]);
                        }
                    } else {
                        DB::rollBack();
                        throw ValidationException::withMessages([
                            'purchase_count' => __('epin.current_active_pin_count_greater_than_max_active_count'),
                        ]);
                    }
                } else {
                    throw ValidationException::withMessages([
                        'purchase_amount' => __('epin.balance_amount_not_sufficient'),
                    ]);
                }
            } else {
                throw ValidationException::withMessages([
                    'purchase_amount' => __('epin.check_pincount_or_amount'),
                ]);
            }
        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Error',
                'errors' => $exception->errors(),
            ], 422);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => $th->getMessage(),
            ], 404);
        }
    }

    public function getBalanceAmount($user_id)
    {
        $balance = UserBalanceAmount::where('user_id', $user_id)->with('user.userdetails')->first();

        return $balance;
    }

    public function getEpinAmount($amount_id)
    {
        $pinAmount = PinAmountDetails::find($amount_id);

        return $pinAmount->amount;
    }

    public function epinTransfer(EpinTransferRequest $request)
    {
        DB::beginTransaction();
        try {
            $fromUser = $request->from_user;
            $toUser = $request->to_user;
            $epin = $request->epin;
            $epin = PinNumber::find($epin);
            $epin->update([
                'allocated_user' => $toUser,
            ]);

            EpinTransferHistory::create([
                'to_user' => $toUser,
                'from_user' => $fromUser,
                'epin_id' => $epin->id,
                'ip' => $request->ip(),
                'done_by' => auth()->user()->id,
                'activity' => 'E-pin Transfered',
                'date' => now(),
            ]);

            DB::commit();

            if ($request->ajax()) {
                $view = view('admin.epin.ajax.epinTransferUser', compact('epin'));

                return response()->json([
                    'status' => true,
                    'message' => trans('epin.epinTransferSuccess'),
                    'user' => $view->render(),
                    'id' => $epin->id,

                ], 200);
            } else {
                return redirect(route('epin.index'))->with('success', trans('epin.epinTransferSuccess'));
            }
        } catch (Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 400);
            } else {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }

    public function getUserEpinList(Request $request)
    {
        $request->validate([
            'from_user' => ['required', 'exists:users,id', new EpinUserTransferRule],
        ]);

        $username = $request->from_user;
        $epins = PinNumber::AllocateUser($username)->NonExpired()->where('balance_amount', '>', 0)->ActivePurchaseStatus()->get();
        $epinList = [];
        foreach ($epins as $epin){
            $epinCount = EpinTransferHistory::where('epin_id', $epin->id)
                ->count();
                if ($epinCount == 0) {
                    $epinList[] = $epin;
                }
        }
        $view = view('admin.epin.ajax.epinList', compact('epinList'));

        return response()->json([
            'status' => true,
            'epinList' => $view->render(),
        ]);
    }

    public function delete(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {

            foreach ($request->epins as $key => $epin) {
                $epin = PinNumber::find($epin['pin_id']);
                $epin->update([
                    'status' => 'deleted',
                ]);
                if ($epin->purchase_status && $epin->expiry_date >= now()) {
                    $ewalletService = new EwalletService;
                    $userBalance = UserBalanceAmount::find($epin->allocated_user);
                    $userBalance->balance_amount = $userBalance->balance_amount + $epin->balance_amount;
                    $userBalance->push();
                    $toUser = User::find($epin->allocated_user);
                    // $ewalletService->addToEwalletHistory($this->moduleStatus(), null, $toUser, $epin->id, $epin->balance_amount, 'pin_purchase_delete', 'credit', null, 0, null, 'pin_purchase');
                    $ewalletService->addToEwalletTransferHistory($this->moduleStatus(), null, $toUser, null, $epin->balance_amount, 'pin_purchase_delete', 'credit', null, 0, null);
                }
            }

            DB::commit();
            if ($request->ajax()) {
                $epins = PinNumber::Active()->NonExpired();

                return response()->json([
                    'status' => true,
                    'message' => trans('epin.epinDeleteSuccess'),
                    'count' => $epins->count(),
                    'balance' => $epins->sum('balance_amount'),
                ], 200);
            } else {
                return redirect(route('epin.index'))->with('success', trans('epin.epinDeleteSuccess'));
            }
        } catch (Throwable $th) {
            if ($request->ajax()) {
                DB::rollBack();

                return response()->json([
                    'message' => $th,

                ], 400);
            } else {
                DB::rollBack();

                return redirect(route('epin.index'))->with('error', $th->getMessage());
            }
        }
    }

    public function deleteRequestedEpin(Request $request, $id = null)
    { 
        try {
            DB::beginTransaction();
            foreach ($request->epins as $key => $epin) {
                $pinId = $epin['pin_id'];
                $count = $epin['count'];
                $pinRequest = PinRequest::find($pinId);
                if ($count == $pinRequest->requested_pin_count) {
                    $pinRequest->update([
                        'status' => 0,
                    ]);
                } else {
                    $balanceCount = $pinRequest->requested_pin_count - $count;
                    $pinRequest->update([
                        'requested_pin_count' => $balanceCount,
                    ]);
                }
            DB::commit();
            }
            if ($request->ajax()) {
                $count = PinRequest::Active()->count();

                return response()->json([
                    'status' => true,
                    'message' => trans('epin.requestEpinDeleteMsg'),
                    'pendingCount' => $count,
                ], 200);
            } else {
                return redirect(route('epin.index'))->with('success', trans('epin.requestEpinDeleteMsg'));
            }
        } catch (Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th,
                ], 400);
            } else {
                return redirect(route('epin.index'))->with('error', $th->getMessage());
            }
        }
    }

    public function filterPendingRequest(Request $request)
    {
        $request->validate([
            'username' => 'required',
        ]);

        $requests = PinRequest::where('user_id', $request->username)->Active()->get();
        $currency = currencySymbol();

        $view = view('admin.epin.ajax.pendingRequest', compact('requests', 'currency'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ]);
    }

    public function allocatePendingEpins(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            foreach ($request->epins as $key => $epin) {
                $pinId = $epin['pin_id'];
                $count = $epin['count'];
                $pinRequest = PinRequest::find($pinId);

                for ($i = 1; $i <= $count; $i++) {
                    $pinNumber = generatePinNumber();
                    PinNumber::create([
                        'numbers' => $pinNumber,
                        'alloc_date' => now(),
                        'generated_user' => auth()->user()->id,
                        'allocated_user' => $pinRequest->user_id,
                        'uploaded_date' => now(),
                        'expiry_date' => $pinRequest->expiry_date,
                        'amount' => $pinRequest->pin_amount,
                        'balance_amount' => $pinRequest->pin_amount,
                    ]);
                }

                if ($count == $pinRequest->requested_pin_count) {
                    $pinRequest->update([
                        'status' => 0,
                    ]);
                } else {
                    $balanceCount = $pinRequest->requested_pin_count - $count;
                    $pinRequest->update([
                        'requested_pin_count' => $balanceCount,
                    ]);
                }
            }



            // TODO-------->opininon from tester commented on 08-12-2022 10.11 AM <------------

            // $ewalletService = new EwalletService;
            // $toUser         = User::findOrfail($pinRequest->user_id);
            // $ewalletService->addToEwalletHistory($this->moduleStatus(), null, $toUser, $pinRequest->id, $pinRequest->pin_amount * $count, 'pin_purchase', 'debit', null, 0, null, 'pin_purchase');
            // $userBalance = UserBalanceAmount::find($pinRequest->user_id);
            // $userBalance->balance_amount = $userBalance->balance_amount - ($pinRequest->pin_amount * $count);
            // if ($userBalance->balance_amount - ($pinRequest->pin_amount * $count) < 0) {
            //     DB::rollBack();
            //     if ($request->ajax()) {
            //         return response()->json([
            //             'status' => false,
            //             'message' => trans('epin.low_balance'),
            //         ], 422);
            //     } else {
            //         return redirect(route('epin.index'))->with('error', trans('epin.low_balance'));
            //     }
            // }
            // $userBalance->push();

            DB::commit();
            $this->sendEpinApprovedNotification($pinRequest->user_id, $count, $pinRequest->pin_amount, $id);
            if ($request->ajax()) {
                $currency = currencySymbol();
                $balanceAmount = PinNumber::Active()->NonExpired()->sum('balance_amount');
                $count = PinRequest::Active()->count();
                $activePins = PinNumber::Active()->NonExpired()->count();

                return response()->json([
                    'status' => true,
                    'message' => trans('epin.pinAllocatedMsg'),
                    'balanceAmount' => $currency . ' ' . formatCurrency($balanceAmount),
                    'pendingCount' => $count,
                    'activePins' => $activePins,

                ], 200);
            } else {
                return redirect(route('epin.index'))->with('success', trans('epin.pinAllocatedMsg'));
            }
        } catch (Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 400);
            } else {
                return redirect(route('epin.index'))->with('error', $th->getMessage());
            }
        }
    }

    public function statusChange(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            foreach ($request->epins as $key => $epin) {
                $pin = PinNumber::find($epin['pin_id']);
                $currency = currencySymbol();
                if ($pin->status == 'active') {
                    $pin->update([
                        'status' => 'blocked',
                    ]);
                    $status = $pin->status;
                    // $msg = trans('epin.epinBlock');
                } elseif ($pin->status == 'blocked') {
                    $pin->update([
                        'status' => 'active',
                    ]);
                    $status = $pin->status;
                    // $msg = trans('epin.epinActive');
                }
            }
            DB::commit();

            if ($request->ajax()) {
                $activePinNumbers = PinNumber::with('allocatedUser.userDetail')->latest()->NonExpired()->paginate(10);
                $balanceAmount = PinNumber::Active()->NonExpired()->sum('balance_amount');
                $activePins = $activePinNumbers->total();
                $currency = currencySymbol();
                $view = view('admin.epin.ajax.activeEpins', compact('activePinNumbers', 'currency'));

                return response()->json([
                    'status' => true,
                    'message' => trans('epin.epinStatus'),
                    'pinStatus' => $status,
                    'data' => $view->render(),
                    'balanceAmount' => number_format($balanceAmount, 2),
                    'activePins' => $activePins,

                ]);
            } else {
                return redirect(route('epin.index'))->with('success', trans('epin.epinStatus'));
            }
        } catch (Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 422);
            } else {
                return redirect(route('epin.index'))->with('error', $th);
            }
        }
    }

    public function getEpins(Request $request)
    {
        if ($request->has('term')) {
            $string = $request->term;
            $pin = PinNumber::Like($string)->get()->all();

            return response()->json([
                'status' => true,
                'data' => $pin,
            ]);
        }
    }

    public function checkEpinAvailabilityForCart(Request $request)
    {
        $request->validate([
            'epinOld.*' => ['sometimes', 'array'],
            'epin' => ['sometimes', 'exists:pin_numbers,numbers', function ($attribute, $value, $fail) use ($request) {
                if ($request->epinOld) {
                    $oldEpinValues = collect($request->epinOld)->pluck('value')->all();
                    if (in_array($value, $oldEpinValues)) {
                        $fail(__('epin.epin_already_taken', ['epin' => $value]));
                    }
                }
            }],
            'totalAmount' => 'required|min:1',
            'packageId' => 'sometimes|required|exists:packages,id',
        ]);
        $moduleStatus = $this->moduleStatus();
        $productPrice = 0;
        $currency = currencySymbol();

        $totalPrice = $request->totalAmount;
        $epin = $request->epin;
        $userId = User::GetAdmin()->id;
        $oldPins = $request->epinOld ?? [];

        if (auth()->user()->user_type == 'employee') {
            $userId = User::GetAdmin()->id;
        } else {
            $userId = auth()->user()->id;
        }
        $pinNumber = ($epin) ? PinNumber::NonExpired()->AllocateUser($userId)
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
            ? PinNumber::NonExpired()->AllocateUser($userId)->WhereIn('id', [...$oldPinsIds])->get()->sum('balance_amount')
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

    public function sendEpinApprovedNotification($userId, $count, $amount, $requestId)
    {

        $userSchema = User::find($userId);
        $epinData = [
            'username' => $userSchema->username,
            'userId' => $userId,
            'amount' => $amount,
            'count'  => $count,
            'requestId' => $requestId,
        ];

        return Notification::send($userSchema, new EpinApprovedNotification($epinData));

        dd('Task completed!');
    }
}
