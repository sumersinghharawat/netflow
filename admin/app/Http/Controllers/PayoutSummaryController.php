<?php

namespace App\Http\Controllers;

use App\Jobs\UserActivityJob;
use App\Models\AmountPaid;
use App\Models\PaymentGatewayConfig;
use App\Models\PayoutConfiguration;
use App\Models\PayoutReleaseRequest;
use App\Models\User;
use App\Models\UserBalanceAmount;
use App\Services\EwalletService;
use App\Services\PayoutService;
use App\Services\PaypalService;
use App\Services\SendMailService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Notification;
use App\Notifications\PayoutReleasedNotification;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PayoutSummaryController extends CoreInfController
{
    protected $serviceClass;

    public function __construct(EwalletService $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    public function index()
    {
        $moduleStatus = $this->moduleStatus();
        $payoutRelease = PayoutConfiguration::first()->release_type;
        $paymentGateWay = PaymentGatewayConfig::ActivePayout()->Payoutascorder()->get();
        $data = $this->getSummaryAmounts();
        $data = [...$data];
        $currency = currencySymbol();

        return view('admin.payoutSummary.index', compact('moduleStatus', 'payoutRelease', 'paymentGateWay', 'data', 'currency'));
    }

    public function getSummaryAmounts()
    {
        $pendingAmount = PayoutReleaseRequest::Pending()->sum('balance_amount');
        $approvedAmount = AmountPaid::Pending()->Released()->BankTransfer()->sum('amount');
        $paidAmount = AmountPaid::Released()->Paid()->sum('amount');
        $rejected = PayoutReleaseRequest::Rejected()->sum('amount');
        $currency = currencySymbol();
        $data = [
            'pending' => $currency . ' ' . formatCurrency($pendingAmount),
            'approved' => $currency . ' ' . formatCurrency($approvedAmount),
            'paid' => $currency . ' ' . formatCurrency($paidAmount),
            'rejected' => $currency . ' ' . formatCurrency($rejected),
        ];

        return $data;
    }

    public function getPayoutType()
    {
        $payoutRelease = PayoutConfiguration::first()->release_type;

        switch ($payoutRelease) {
            case 'from_ewallet':
                $payoutType = 'admin';
                break;
            case 'ewallet_request':
                $payoutType = 'user';
                break;
            case 'both':
                $payoutType = 'admin';
                break;
            default:
                $payoutType = null;
                break;
        }

        return $payoutType;
    }

    public function getPayoutReleases(Request $request)
    {
        $currency = currencySymbol();
        if ($request->has('payoutReleaseType')) {
            $payoutType = $request->payoutReleaseType;
        } else {
            $payoutType = $this->getPayoutType();
        }
        if ($payoutType == 'admin') {
            $payoutAmountType = 'from_ewallet';
        } else {
            $payoutAmountType = 'ewallet_request';
        }

        if ($payoutAmountType == 'ewallet_request') {
            $paymentMethod = PaymentGatewayConfig::find($request->payment_method);
            $payoutDetails = PayoutReleaseRequest::with(['user.userDetails', 'user.userBalance', 'user.userDetails.payout'])->Pending()->PaymentMethod($paymentMethod->id);
        } else {
            $amount = PayoutConfiguration::first()->min_payout;
            $payoutDetails = UserBalanceAmount::with('user.userDetails.payout')->BalanceAmount($amount);

            $amount = formatCurrency($amount);
        }
        if ($request->has('users')) {
            $payoutDetails->whereIn('user_id', [...$request->users]);
        }

        if ($request->has('payment_method')) {
            $paymentMethod = $request->payment_method;

            $payoutDetails->whereRelation('user.userDetails', 'payout_type', $paymentMethod);
        }

        if ($request->has('kycStatus')) {
            $kycStatus = $request->kycStatus;
            if ($kycStatus == 'active') {
                $payoutDetails->whereRelation('user.userDetails', 'kyc_status', true);
            } else {
                $payoutDetails->whereRelation('user.userDetails', 'kyc_status', false);
            }
        }

        $payoutDetails->whereRelation('user', [['user_type', 'user'], ['active', true]]);

        return DataTables::of($payoutDetails)
            ->addColumn('checkbox', function ($data) {
                return '<input type="checkbox" name="approve" id="btn" class="form-check-input mt-3 checked-box" value="' . $data->id . '">';
            })
            ->addColumn('member_name', function ($data) {
                return '<div class="d-flex"><img class="rounded-circle avatar-md" src="' . asset('/assets/images/users/avatar-1.jpg') . '"><div class="transaction-user"><h5>' . $data->user->userDetail->name . '</h5><span>' . $data->user->username . '</span></div></div';
            })
            ->addColumn('action', function ($data) use ($payoutType) {
                if ($payoutType == 'admin') {
                    return '
                    <div class="popup-btn-area col-8 d-none" id="reg_approval_action_popup">
                    <div class="row">
                        <div class="text-white col">
                            <span id="active_items_selected_span"></span>
                            <!-- <div id="active_items_selected_div_new"></div> -->
                        </div>
                        <div class="col">
                    <div class="gap-2 d-flex">
                <a href="#" onclick="approvePayoutRelease()" class="btn btn-success">
                <i class="bx bx-check-circle"></i></a></div>
                </div>

                    </div>
                </div>';
                } else {
                    return '
                    <div class="popup-btn-area col-8 d-none" id="reg_approval_action_popup">
                    <div class="row">
                        <div class="text-white col">
                            <span id="active_items_selected_span"></span>
                            <!-- <div id="active_items_selected_div_new"></div> -->
                        </div>
                        <div class="col">
                    <div class="gap-2 d-flex">
                <a href="#" onclick="approvePayoutReleaseUserRequest()" class="btn btn-success">
                <i class="bx bx-check-circle"></i></a>
                <a href="#" onclick="rejectPayoutReleaseUserRequest()" class="btn btn-danger">
                <i class="bx bx-trash"></i></a>
                </div>
                </div>

                    </div>
                </div>';
                }
            })
            ->editColumn('balance', fn ($data) => ($payoutAmountType == 'ewallet_request') ? $currency . ' ' . formatCurrency($data->user->userBalance->balance_amount) : $currency . ' ' . formatCurrency($data->balance_amount))
            ->editColumn('payment_method', fn ($data) => ucfirst($data->user->userDetail->payout_type))
            ->editColumn('payout_type', fn () => ($payoutType == 'admin') ? 'Manual' : 'User Request')
            ->editColumn('amount', fn ($data) => ($payoutType == 'admin') ? "<input type='number' value='$amount' min='0' id='adminPayout_$data->id'>" : $currency . ' ' . formatCurrency($data->balance_amount))
            ->editColumn('payment_method', fn ($data) => ucfirst($data->user->userDetail->payout->name))
            ->rawColumns(['member_name', 'payment_method', 'payout_type', 'balance', 'amount', 'checkbox', 'action', 'payment_method'])
            ->make(true);
    }

    public function releaseManualPayouts(Request $request,$data)
    {

        $i=0;
        try {
            $ids = explode(',', $data);
            foreach ($ids as $id){
                DB::beginTransaction();
                $request->validate([
                    'amount' => [
                        'required',
                        'array',
                        'min:1',
                        function ($attribute, $value, $fail) {
                            if (!is_array($value) || empty($value) || !collect($value)->every(function ($item) {
                                return is_numeric($item) && $item > 0;
                            })) {
                                $fail('The '.$attribute.' must be an array with all numeric values greater than 0.');
                            }
                        },
                    ],
                ]);
                $prefix = session()->get('prefix');

                $payoutConfig = PayoutConfiguration::first();
                $payoutReleaseType = $payoutConfig->release_type;
                $minPayout = $payoutConfig['min_payout'];
                $maxPayout = $payoutConfig['max_payout'];
                $moduleStatus = $this->moduleStatus();
                $kycStatus = $moduleStatus->kyc_status;
                $payoutType = $this->getPayoutType();
                $userBalance = UserBalanceAmount::with('user.userDetails')->find($id);
                $paymentMethod = $userBalance->user->userDetails->payout_type;
                $paymentGateWay = PaymentGatewayConfig::find($paymentMethod);
                $payoutService = new PayoutService;
                $userName = $userBalance->user['username'];

                if ($kycStatus) {
                    $userKycUploadStatus =  $userBalance->user->userDetails->kyc_status;
                    if (!$userKycUploadStatus) {
                        return response()->json([
                            'status'     =>        false,
                            'message'    =>        trans('payout.kyc_not_upload') . ' ' . $userBalance->user['username'],
                        ], 422);
                    }
                }
                if ($payoutType == 'admin') {
                    $releaeseAmount = defaultCurrency($request->amount[$i]);
                    $payoutFee = $this->calculatePayoutFee($releaeseAmount);
                    $userWalletDeduct = $releaeseAmount + $payoutFee;
                    $balanceAmount = $userBalance->balance_amount;

                    if ($userWalletDeduct > $balanceAmount) {
                        return response()->json([
                            'status' => false,
                            'message' => trans('payout.low_balance'),
                        ], 422);
                    }
                }
                if ($releaeseAmount > $maxPayout || $releaeseAmount < $minPayout) {
                    return response()->json([
                        'status'        =>      false,
                        'message'       =>      trans('payout.cant_release_amount') . ' ' . $userBalance->user['username'],
                    ], 422);
                }
                if ($paymentGateWay->slug == 'bank-transfer' && ($payoutReleaseType == 'from_ewallet' || $payoutReleaseType == 'both')) {
                    if ($balanceAmount >= $releaeseAmount && $releaeseAmount > 0) {
                        $balance = round(($balanceAmount - $releaeseAmount), 8);

                        $amountPaid = AmountPaid::create([
                            'user_id' => $userBalance->user_id,
                            'amount' => $releaeseAmount,
                            'date' => now(),
                            'transaction_id' => 0,
                            'type' => 'released',
                            'status' => 0,
                            'payment_method' => $paymentMethod,
                            'payout_fee' => $payoutFee,
                        ]);
                        $refernceId = $amountPaid->id ?? ' ';

                        $moduleStatus = $this->moduleStatus();
                        // $addToEwalletHistory = $this->serviceClass->addToEwalletHistory($moduleStatus, null, $userBalance->user, $refernceId, $releaeseAmount, 'payout_release_manual', 'debit', null, $payoutFee, null, 'payout');
                        $this->serviceClass->addToEwalletPurchaseHistory($moduleStatus, $userBalance->user, $refernceId, 'payout', $releaeseAmount, $balance, 'payout_release_manual', 'payout');

                        if ($payoutFee > 0) {
                            $this->serviceClass->addToEwalletPurchaseHistory($moduleStatus, $userBalance->user, $refernceId, 'payout', $payoutFee, ($balance - $payoutFee), 'payout_fee', 'payout');
                        }

                        $userBalance->update([
                            'balance_amount' => $balance,
                        ]);
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'status'        =>      false,
                            'message'       =>      trans('payout.cant_release_amount') . ' ' . $userBalance->user['username'],
                        ], 422);
                    }
                    $refernceId = ($refernceId) ? $refernceId : '';
                } elseif ($paymentGateWay->slug == 'paypal' && ($payoutReleaseType == 'from_ewallet' || $payoutReleaseType == 'both')) {
                    $userEmail = $userBalance->user->userDetails->paypal;
                    $userName = $userBalance->user['username'];
                    if ($userEmail == "NA" || $userEmail == "NULL" || $userEmail == "") {
                        return response()->json([
                            'status'        =>      false,
                            'message'       =>      trans('payout.paypal_email_not_provided', ['username' => $userName]),
                        ], 422);
                    }

                    $paypalService = new PaypalService;

                    $paypalBalance =  $paypalService->payPalBalance();
                    if ($paypalBalance < $releaeseAmount) {
                        return response()->json([
                            'status'        =>      false,
                            'message'       =>      trans('payout.not_enough_payapal_balance'),
                        ], 422);
                    }
                    $email = base64_decode($userEmail);
                    $senderBatchId = 'Payout_' . now()->format('m.d.y.h:i:s');
                    $payoutData = [
                        'payment_method' => $paymentMethod,
                        'user_id'   => $userBalance->user_id,
                        'payout_fee' => $payoutFee,
                        'released_amount' => $releaeseAmount,
                    ];
                    $result  = $payoutService->paypalPayout($userBalance->user, $email, $senderBatchId, $releaeseAmount, $payoutData, $prefix, $moduleStatus);
                    if ($result) {
                        $balance = round(($balanceAmount - $userWalletDeduct), 8);
                        $userBalance->update([
                            'balance_amount' => $balance,
                        ]);
                    } else {
                        return response()->json([
                            'status'        =>      false,
                            'message'       =>      'payout failed by paypal side',
                        ], 422);
                    }
                } elseif ($paymentGateWay->slug == 'stripe' && ($payoutReleaseType == 'from_ewallet' || $payoutReleaseType == 'both')) {
                    $userStripe = $userBalance->user->userDetails->stripe;
                    $userName = $userBalance->user['username'];
                    if ($userStripe == "NA" || $userStripe == "NULL" || $userStripe == "") {
                        return response()->json([
                            'status'        =>      false,
                            'message'       =>      trans('payout.stripe_account_not_provided', ['username' => $userName]),
                        ], 422);
                    }

                    $stripeService = new StripeService;

                    $stripeBalance = $stripeService->getBalance();

                    if ($stripeBalance < $releaeseAmount) {
                        return response()->json([
                            'status'        =>      false,
                            'message'       =>      trans('payout.not_enough_stripe_balance'),
                        ], 422);
                    }
                    $account = base64_decode($userStripe);
                    $payoutStatus = $stripeService->getAccountRetriveDetails($account);


                    if ($payoutStatus && $payoutStatus->capabilities->card_payments == 'active' && $payoutStatus->capabilities->transfers == 'active' && $payoutStatus->payout_enabled) {
                        $payoutData = [
                            'payment_method' => $paymentMethod,
                            'user_id'   => $userBalance->user_id,
                            'payout_fee' => $payoutFee,
                            'released_amount' => $releaeseAmount,
                        ];

                        $result = $stripeService->stripePayout($moduleStatus, $userBalance->user, $payoutData, $account);

                        if ($result) {
                            $balance = round(($balanceAmount - $userWalletDeduct), 8);
                            $userBalance->update([
                                'balance_amount' => $balance,
                            ]);
                        } else {
                            return response()->json([
                                'status'        =>      false,
                                'message'       =>      'payout failed by paypal side',
                            ], 422);
                        }
                    } else {
                        return response()->json([
                            'status'     =>     false,
                            'message'    =>     trans('payout.incomplete_stripe_account_details_for', ['username' => $userName]),
                        ], 422);
                    }
                }
                DB::commit();
                $this->sendMailtoUser($userBalance->user_id, 'payout_release');
                $this->sendPayoutReleaseNotification($userBalance->user_id, $userName, $releaeseAmount, '');


                UserActivityJob::dispatch(
                    auth()->user()->id,
                    [],
                    'payout_release_manual',
                    "{release manual payout for $userName, amount $releaeseAmount and payout fee  $payoutFee}",
                    "{$prefix}_",
                    auth()->user()->user_type

                );
                $i++;
            }
                return response()->json([
                    'status' => true,
                    'message' => trans('payout.releaese_success'),
                ]);
            } catch (Throwable $th) {
                DB::rollBack();
                dd($th);

                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 400);
            }
    }


    public function calculatePayoutFee($amount)
    {
        $config = PayoutConfiguration::first();
        $fee = $config['fee_amount'];
        if ($config['fee_mode'] == 'percentage') {
            $fee = $amount * $config['fee_amount'] / 100;
        }

        return $fee;
    }

    public function sendMailtoUser($userId, $type = 'payout_release')
    {
        $sendDetails = [];
        $user = User::with('userDetail')->find($userId);

        // $sendDetails['email'] = $user->userDetail->email;
        $sendDetails['email'] = $user->email;
        $sendDetails['first_name'] = $user->userDetail->name;
        $sendDetails['last_name'] = $user->userDetail->second_name ?? '';
        $sendDetails['full_name'] = $sendDetails['first_name'] . ' ' . $sendDetails['last_name'];

        $serviceClass = new SendMailService;
        return $serviceClass->sendAllEmails($type, $user, $sendDetails);
    }

    public function releaseUserRequestPayouts($data)
    {
        try {
            $ids = explode(',', $data);
            foreach ($ids as $id){
                    DB::beginTransaction();
                    $payoutConfig = PayoutConfiguration::first();
                    $payoutReleaseType = $payoutConfig->release_type;
                    $minPayout = $payoutConfig['min_payout'];
                    $maxPayout = $payoutConfig['max_payout'];
                    $moduleStatus = $this->moduleStatus();
                    $kycStatus = $moduleStatus['kyc_status'];
                    $payoutType = $this->getPayoutType();
                    $payoutService = new PayoutService;
                    $prefix = session()->get('prefix');

                    $payoutRelease      =   PayoutReleaseRequest::with('user.userDetails', 'paymentMethod')->find($id);
                    $paymentMethod      =  $payoutRelease->paymentMethod;
                    // $payoutRelease->whereRelation('user.userDetails', 'payout_type', $paymentMethod);

                    $userName = $payoutRelease->user['username'];

                    if ($kycStatus) {
                        $userKycUploadStatus =  $payoutRelease->user->userDetails->kyc_status;
                        if (!$userKycUploadStatus) {
                            return response()->json([
                                'status'     =>        false,
                                'message'    =>        trans('payout.kyc_not_upload') . ' ' . $payoutRelease->user['username'],
                            ], 422);
                        }
                    }
                    if ($payoutType == 'user' || $payoutReleaseType == 'both') {
                        $releaeseAmount = defaultCurrency($payoutRelease['balance_amount']);
                        $payoutFee = $payoutRelease['payout_fee'];
                    }
                    if ($releaeseAmount > $maxPayout || $releaeseAmount < $minPayout) {
                        return response()->json([
                            'status' => false,
                            'message' => trans('payout.cant_release_amount') . $payoutRelease->user['username'],
                        ], 422);
                    }
                    if ($paymentMethod->slug != 'paypal' && $paymentMethod->slug != 'Bitgo' && $paymentMethod->slug != 'stripe') {
                        if ($payoutReleaseType == 'ewallet_request' || $payoutReleaseType == 'both') {
                            $payoutRelease->update([
                                'status' => 1,
                            ]);
                        }

                        $amountPaid = AmountPaid::create([
                            'user_id' => $payoutRelease->user_id,
                            'amount' => $releaeseAmount,
                            'date' => now(),
                            'transaction_id' => 0,
                            'type' => 'released',
                            'status' => 0,
                            'payment_method' => $paymentMethod->id,
                            'payout_fee' => $payoutFee,
                            'request_id' => $payoutRelease->id,
                        ]);
                        $refernceId = $amountPaid->id ?? ' ';
                        $transactionId = $payoutRelease->id;
                        $moduleStatus = $this->moduleStatus();
                        // $addToEwalletHistory = $this->serviceClass->addToEwalletHistory($moduleStatus, null, $payoutRelease->user, $refernceId, $releaeseAmount, 'payout_release', 'debit', $transactionId, $payoutFee, null, 'payout');
                        $updateEwalletHisory = $this->serviceClass->updateEwalletHistory($payoutRelease->id, 'payout');
                        // dd('here');
                    }

                    if ($paymentMethod->slug == 'paypal') {
                        if ($payoutReleaseType == 'ewallet_request' || $payoutReleaseType == 'both') {
                            $userEmail = $payoutRelease->user->userDetails->paypal;
                            $userName = $payoutRelease->user['username'];
                            if ($userEmail == "NA" || $userEmail == "NULL" || $userEmail == "") {
                                return response()->json([
                                    'status'        =>      false,
                                    'message'       =>      trans('payout.paypal_email_not_provided', ['username' => $userName]),
                                ], 422);
                            }

                            $paypalService = new PaypalService;

                            $paypalBalance =  $paypalService->payPalBalance();

                            if ($paypalBalance < $releaeseAmount) {
                                return response()->json([
                                    'status'        =>      false,
                                    'message'       =>      trans('payout.not_enough_payapal_balance'),
                                ], 422);
                            }
                            $email = base64_decode($userEmail);
                            $payoutData = [
                                'payment_method' => $paymentMethod->id,
                                'user_id'   => $payoutRelease->user_id,
                                'payout_fee' => $payoutFee,
                                'released_amount' => $releaeseAmount,
                            ];
                            $senderBatchId = 'Payout_' . now()->format('m.d.y.h:i:s');
                            $result = $payoutService->paypalPayout($payoutRelease->user, $email, $senderBatchId, $releaeseAmount, $payoutData, $prefix, $moduleStatus, $payoutRelease->id);

                            if ($result) {
                                $payoutRelease->update([
                                    'status' => 1,
                                ]);
                            } else {
                                return response()->json([
                                    'status'        =>      false,
                                    'message'       =>      'payout failed by paypal side',
                                ], 422);
                            }
                        }
                    } elseif ($paymentMethod->slug == 'stripe' && ($payoutReleaseType == 'from_ewallet' || $payoutReleaseType == 'both')) {
                        $userStripe = $payoutRelease->user->userDetails->stripe;
                        $userName = $payoutRelease->user['username'];
                        if ($userStripe == "NA" || $userStripe == "NULL" || $userStripe == "") {
                            return response()->json([
                                'status'        =>      false,
                                'message'       =>      trans('payout.stripe_account_not_provided', ['username' => $userName]),
                            ], 422);
                        }

                        $stripeService = new StripeService;

                        $stripeBalance = $stripeService->getBalance();

                        if ($stripeBalance < $releaeseAmount) {
                            return response()->json([
                                'status'        =>      false,
                                'message'       =>      trans('payout.not_enough_stripe_balance'),
                            ], 422);
                        }
                        $account = base64_decode($userStripe);
                        $payoutStatus = $stripeService->getAccountRetriveDetails($account);


                        if ($payoutStatus && $payoutStatus->capabilities->card_payments == 'active' && $payoutStatus->capabilities->transfers == 'active' && $payoutStatus->payout_enabled) {
                            $payoutData = [
                                'payment_method' => $paymentMethod,
                                'user_id'   => $payoutRelease->user_id,
                                'payout_fee' => $payoutFee,
                                'released_amount' => $releaeseAmount,
                            ];

                            $result = $stripeService->stripePayout($moduleStatus, $payoutRelease->user, $payoutData, $account, $payoutRelease->id);


                            if ($result) {
                                $payoutRelease->update([
                                    'status' => 1,
                                ]);
                            } else {
                                return response()->json([
                                    'status'        =>      false,
                                    'message'       =>      'payout failed by paypal side',
                                ], 422);
                            }
                        } else {
                            return response()->json([
                                'status'     =>     false,
                                'message'    =>     trans('payout.incomplete_stripe_account_details_for', ['username' => $userName]),
                            ], 422);
                        }
                    }
                    DB::commit();
                    UserActivityJob::dispatch(
                        auth()->user()->id,
                        [],
                        'release user request Payouts',
                        "{release user requested payout for $userName, amount $releaeseAmount and payout fee  $payoutFee}",
                        "{$prefix}_",
                        auth()->user()->user_type
                    );

                    $this->sendMailtoUser($payoutRelease->user_id, 'payout_release');
                    $this->sendPayoutReleaseNotification($payoutRelease->user_id, $userName, $releaeseAmount, $payoutRelease->id);
                }
                    return response()->json([
                        'status' => true,
                        'message' => trans('payout.releaese_success'),
                    ]);
            } catch (Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 400);
        }
    }

    public function rejectPayoutReleaseRequest($data)
    {
        try {
            $ids = explode(',', $data);
            foreach ($ids as $id){
                    DB::beginTransaction();
                    $payoutRelease = PayoutReleaseRequest::with('paidAmounts', 'user.userBalance')->find($id);

                    $payoutRelease->update([
                        'status' => 2,
                    ]);

                    $payoutFee = $payoutRelease['payout_fee'];
                    $balanceAmount = $payoutRelease['balance_amount'];
                    $refundAmonut = round($balanceAmount + $payoutFee, 8);
                    $moduleStatus = $this->moduleStatus();
                    $refernceId = $payoutRelease->paidAmonuts->id ?? null;

                    $this->serviceClass->addToEwalletTransferHistory($moduleStatus, null, $payoutRelease->user, $refernceId, $refundAmonut, 'payout_delete', 'credit', null, $payoutFee, null, 'payout');
                    $refundTotal = $payoutRelease->user->userBalance->balance_amount + $refundAmonut;
                    $payoutRelease->user->userBalance->update([
                        'balance_amount' => $refundTotal,
                    ]);
                    $username = $payoutRelease->user->username;
                    $prefix = session()->get('prefix');
                    DB::commit();
                    UserActivityJob::dispatch(
                        auth()->user()->id,
                        [],
                        'reject payout release rquest',
                        "{reject payout request for $username , amount $balanceAmount and payout fee  $payoutFee}",
                        "{$prefix}_",
                        auth()->user()->user_type

                    );
                }
                    return response()->json([
                        'status' => true,
                        'message' => trans('payout.requestDeleteSuccess'),
                    ]);
            } catch (Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 400);
            }
    }

    public function processPayment(Request $request)
    {
        $currency = currencySymbol();
        $processPayment = AmountPaid::with('user.userDetails', 'paymentMethod')->Pending()->Released();
        if ($request->has('users')) {
            $processPayment->whereIn('user_id', [...$request->users]);
        }

        return DataTables::of($processPayment)
            ->addColumn('member_name', function ($data) {
                return '<div class="d-flex"><img class="rounded-circle avatar-md" src="' . asset('/assets/images/users/avatar-1.jpg') . '"><div class="transaction-user"><h5>' . $data->user->userDetail->name . '</h5><span>' . $data->user->username . '</span></div></div';
            })

            ->addColumn('amount', function ($data) use ($currency) {
                return $currency . ' ' . formatCurrency($data->amount);
            })
            ->editColumn('payout_date', fn ($data) => $data->created_at->format('F j, Y, g:i a'))
            ->addColumn('action', function ($data) {
                if ($data->paymentMethod->slug == 'paypal' || $data->paymentMethod->slug == 'stripe') {
                    return '<div class="progress-bar progress-bar-striped bg-success p-2"
                    role="progressbar" style="width: 75%;height:10px" aria-valuenow="25"
                    aria-valuemin="0" aria-valuemax="100">
                    ' . trans("common.progress") . '</div>';
                }
                return '
                    <div class="gap-2 d-flex">
                <a href="#" onclick="approveProcessPayment(' . $data->id . ')" class="btn btn-success">
                <i class="bx bx-check-circle"></i></a>
                </div>';
            })
            ->rawColumns(['member_name', 'amount', 'payout_date', 'action'])
            ->make(true);
    }

    public function approveProcessPayment($id)
    {
        try {
            DB::beginTransaction();
            $amountPaid = AmountPaid::find($id);
            $amountPaid->update([
                'status' => 1,
                'created_at' => now(),
            ]);
            DB::commit();

            $prefix = session()->get('prefix');
            UserActivityJob::dispatch(
                auth()->user()->id,
                [],
                'payout mark as paid',
                "processed payout marked as paid",
                "{$prefix}_",
                auth()->user()->user_type

            );

            return response()->json([
                'status' => true,
                'message' => trans('payout.mark_as_paid'),
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function payoutSummary(Request $request)
    {
        if ($request->has('status')) {
            switch ($request->status) {
                case 'paid':
                    $status = "paid";
                    $AmountPaid = AmountPaid::with('user.userDetail', 'paymentMethod:id,name')->Active()->Released();
                    break;
                case 'pending':
                    $status = "pending";
                    $AmountPaid = PayoutReleaseRequest::with(['user.userDetail', 'user.userBalance', 'paymentMethod:id,name'])->Pending();
                    break;
                case 'approved':
                    $status = "approved";
                    $AmountPaid = AmountPaid::with('user.userDetail', 'paymentMethod:id,name')->Pending()->Released();
                    break;
                case 'rejected':
                    $status = "rejected";
                    $AmountPaid = PayoutReleaseRequest::with(['user.userDetails', 'user.userBalance', 'paymentMethod:id,name'])->Rejected();
                default:
                    // code...
                    break;
            }
        }

        $currency = currencySymbol();

        if ($request->has('users')) {
            $AmountPaid->whereIn('user_id', [...$request->users]);
        }

        return DataTables::of($AmountPaid)
            ->addColumn('member_name', function ($data) {
                return '<div class="d-flex"><img class="rounded-circle avatar-md" src="' . asset('/assets/images/users/avatar-1.jpg') . '"><div class="transaction-user"><h5>' . $data->user->userDetail->name . '</h5><span>' . $data->user->username . '</span></div></div';
            })
            ->addColumn('ewallet_balance', function ($data) use ($status) {
                if ($status == 'pending') {
                    return $data->user->userBalance->balance_amount;
                } else {
                    return 0;
                }
            })
            ->addColumn('amount', function ($data) use ($currency) {
                return $currency . ' ' . formatCurrency($data->amount);
            })
            ->addColumn('rejected_date', function ($data) use ($status) {
                if ($status == 'rejected') {
                    return $data->updated_at->format('F j, Y, g:i a');
                } else {
                    return 0;
                }
            })
            ->editColumn('payment_method', fn ($data) => $data->paymentMethod->name)
            ->editColumn('invoice_number', fn ($data) => "PR000" . $data->id)
            ->editColumn('payout_date', fn ($data) => $data->created_at->format('F j, Y, g:i a'))
            ->with([
                'status' => $status,
            ])
            ->rawColumns(['member_name', 'amount', 'invoice_number', 'payout_date', 'ewallet_balance', 'rejected_date', 'payment_method'])
            ->make(true);
    }

    public function sendPayoutReleaseNotification($userId, $userName, $amount, $requestId)
    {

        $userSchema = User::find($userId);
        $payoutData = [
            'username' => $userName,
            'userId' => $userId,
            'amount' => $amount,
            'requestId' => $requestId,
            'url' => url('/payout'),
        ];

        return Notification::send($userSchema, new PayoutReleasedNotification($payoutData));

    }
}
