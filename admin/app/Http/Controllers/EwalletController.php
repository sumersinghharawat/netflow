<?php

namespace App\Http\Controllers;

use App\Models\{
    Compensation,
    EwalletCommissionHistory,
    EwalletPurchaseHistory,
    EwalletTransferHistory,
    LegAmount,
    PaymentGatewayConfig,
    PayoutConfiguration,
    Transaction,
    User,
    UserBalanceAmount,
    Purchasewallethistory,
    EwalletPaymentDetail
};
use App\Http\Requests\{
    FundCreditRequest,
    FundTransferRequest
};
use App\Services\EwalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;


class EwalletController extends CoreInfController
{
    protected $serviceClass;

    public function __construct(EwalletService $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    public function index(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $currency = currencySymbol();
        $transferFee = $this->configuration()->trans_fee;
        $ewalletStatus = PaymentGatewayConfig::checkActive('e-wallet');
        $compensation = Compensation::first();

        $earningsCategories = $this->serviceClass->getEnabledBonuses($moduleStatus, $compensation);
        $ewalletCategories = $this->serviceClass->getEwalletCategories($moduleStatus, $compensation, $ewalletStatus, $earningsCategories);
        $active = 'summary';
        $details = $this->serviceClass->getEwalletReportOverview($moduleStatus, $compensation, $earningsCategories, $ewalletStatus);
        $totalCredit = $details->filter(fn ($detail) => $detail['type'] == 'credit')
            ->reduce(fn ($credit, $detail) => $credit + $detail['amount']);
        $totalDebit = $details->filter(fn ($detail) => $detail['type'] == 'debit')
            ->reduce(fn ($debit, $detail) => $debit + $detail['amount']);

        $total = ['credit' => $totalCredit, 'debit' => $totalDebit];
        $purchaseWalletBalance = $this->serviceClass->purchaseWalletBalance($moduleStatus);
        $commissionEarned = LegAmount::sum('amount_payable');

        return view('ewallet.index', compact(
            'details',
            'total',
            'purchaseWalletBalance',
            'commissionEarned',
            'ewalletCategories',
            'active',
            'earningsCategories',
            'transferFee',
            'currency',
            'moduleStatus'
        ));
    }

    public function ewalletSummaryReport(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $currency = currencySymbol();
        $ewalletStatus = PaymentGatewayConfig::checkActive('e-wallet');
        $compensation = Compensation::first();
        $earningsCategories = $this->serviceClass->getEnabledBonuses($moduleStatus, $compensation);

        $ewalletCategories = $this->serviceClass->getEwalletCategories($moduleStatus, $compensation, $ewalletStatus, $earningsCategories);
        if ($request->fromDate || $request->toDate) {
            $fromDate = $request->fromDate;
            $toDate = $request->toDate;
            $details = $this->serviceClass->getEwalletReportOverview($moduleStatus, $ewalletStatus, $earningsCategories, $ewalletCategories, $fromDate, $toDate);
            $view = view('ewallet.ewalletsummary-dateReport', compact('ewalletCategories', 'details','currency'));

            return response()->json([
                'status' => true,
                'data' => $view->render(),
            ], 200);
        }
    }

    public function ewalletTransaction(Request $request)
    {
        $count  = 0;
        $query  = collect([]);

        $currency                = currencySymbol();
        $isTransactionFeeEnabled = $this->configuration()->trans_fee;
        $isPayoutFeeEnabled      = PayoutConfiguration::first()->fee_amount;
        $categoryCollection      = collect($request->category);
        $compensation            = Compensation::first();
        $moduleStatus            = $this->moduleStatus();
        $ewalletStatus           = PaymentGatewayConfig::checkActive('e-wallet');
        $earningsCategories      = $this->serviceClass->getEnabledBonuses($moduleStatus, $compensation);
        $ewalletCategories       = collect($this->serviceClass->getEwalletCategories($moduleStatus, $compensation, $ewalletStatus, $earningsCategories));
        $fundtransferCollection  = collect(['fund_transfer', 'admin_credit', 'admin_debit']);


        // commission histroy
        $commisionIntersect = $earningsCategories->intersect($categoryCollection);
        if (!$request->has('category') || $commisionIntersect) {
            $data = EwalletCommissionHistory::query();
            $data->select(['ewallet_commission_histories.id', 'user_id', 'leg_amount_id as refference_id', 'amount', 'balance', 'amount_type', DB::raw("'credit' as type"), 'date_added', 'ewallet_commission_histories.created_at as createdAt', 'ewallet_commission_histories.updated_at as updatedAt', DB::raw("'commission' as ewallet_type"), 'purchase_wallet', DB::raw("0 as transaction_fee")]);

            if ($request->has('fromDate') && $request->has('toDate')) {
                $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
                $toDate = Carbon::parse($request->toDate)->format('Y-m-d');
                $data->whereBetween('date_added', [$fromDate, $toDate]);
            }
            if ($request->has('users') && count($request->users) > 0) {
                $data->whereHas('user', fn ($user) => $user->whereIn('id', [...$request->users]));
            } else {
                // $data->whereHas('user', fn ($user) => $user->whereIn('user_type', ['admin', 'user']));
                $data->join('users', 'users.id', '=', 'ewallet_commission_histories.user_id')
                    ->whereIn('users.user_type', ['admin', 'user']);
            }
            if ($request->has('type')) {
                $data->having('type', $request->type);
            }
            if ($request->has('category')) {
                $data->whereIn('amount_type', $request->category);
            }
            $query->push($data);
            $count += $data->count();
        }

        // fund transfer
        $fundtransferIntersect = $fundtransferCollection->intersect($categoryCollection);

        if (!$request->has('category') || $fundtransferIntersect) {
            $data2 = EwalletTransferHistory::query();
            $data2->select(['ewallet_transfer_histories.id', 'user_id', 'fund_transfer_id as refference_id', 'amount', 'balance', 'amount_type', 'type', 'date_added', 'ewallet_transfer_histories.created_at as createdAt', 'ewallet_transfer_histories.updated_at', DB::raw("'fund_transfer' as ewallet_type, NULL as purchase_wallet"), 'transaction_fee']);

            if ($request->has('fromDate') && $request->has('toDate')) {
                $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
                $toDate = Carbon::parse($request->toDate)->format('Y-m-d');
                $data2->whereBetween('date_added', [$fromDate, $toDate]);
            }

            if ($request->has('users') && count($request->users) > 0) {
                $data2->whereHas('user', fn ($user) => $user->whereIn('id', [...$request->users]));
            } else {
                // $data2->whereHas('user', fn ($user) => $user->whereIn('user_type', ['admin', 'user']));
                $data2->join('users', 'users.id', '=', 'ewallet_transfer_histories.user_id')
                    ->whereIn('users.user_type', ['admin', 'user']);
            }

            if ($request->has('type')) {
                $data2->having('type', $request->type);
            }
            if ($request->has('category')) {
                $fund_transfer      = ['user_debit', 'user_credit', 'admin_user_credit', 'admin_user_debit'];
                $categories         = array_diff($request->category, ['fund_transfer', 'user_debit', 'user_credit', 'admin_user_credit', 'admin_user_debit']);
                if (count($categories)) {
                    $data2->whereIn('amount_type', $categories);
                }
                if (in_array('fund_transfer', $request->category)) {
                    $data2->orWhereIn('amount_type', $fund_transfer);
                }
            }
            $query->push($data2);
            $count += $data2->count();
        }

        // fund transfer fee
        if ($isTransactionFeeEnabled && (!$request->has('category') || in_array('fund_transfer_fee', $request->category))) {
            $data3 = EwalletTransferHistory::query();
            $data3->select(['ewallet_transfer_histories.id', 'user_id', 'fund_transfer_id', 'amount', 'balance', 'amount_type', 'type', 'date_added', 'ewallet_transfer_histories.created_at as createdAt', 'ewallet_transfer_histories.updated_at as updatedAt', DB::raw("'fund_transfer_fee' as ewallet_type, NULL as purchase_wallet"), 'transaction_fee']);
            $data3->whereIn('amount_type', ['admin_user_debit', 'user_debit']);

            if ($request->has('fromDate') && $request->has('toDate')) {
                $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
                $toDate = Carbon::parse($request->toDate)->format('Y-m-d');
                $data3->whereBetween('date_added', [$fromDate, $toDate]);
            }

            if ($request->has('users') && count($request->users) > 0) {
                $data3->whereHas('user', fn ($user) => $user->whereIn('id', [...$request->users]));
            } else {
                // $data3->whereHas('user', fn ($user) => $user->whereIn('user_type', ['admin', 'user']));
                $data3->join('users', 'users.id', '=', 'ewallet_transfer_histories.user_id')
                    ->whereIn('users.user_type', ['admin', 'user']);
            }

            if ($request->has('type')) {
                $data3->where(function ($query) use ($request) {
                    foreach ($request->type as $key => $type) {
                        if ($type == 'debit') {
                            $query->orWhereIn('amount_type', ['user_debit', 'admin_user_debit'])
                                ->where('type', 'debit');
                        }
                    }
                });
            }
            if ($request->has('category')) {
                $categories  = ['user_debit', 'admin_user_debit', 'fund_transfer_fee'];
                if (in_array('fund_transfer_fee', $request->category)) {
                    $data3->whereIn('amount_type', $categories);
                }
            }
            $query->push($data3);
            $count += $data3->count();
        }

        // Purchases and payouts
        $diffCategory            = $earningsCategories->merge($fundtransferCollection)->merge(collect(['fund_transfer_fee']));
        $purchaseCategories      = $ewalletCategories->diff($diffCategory);
        $purchaseIntersect       = $purchaseCategories->intersect($categoryCollection);

        if (!$request->has('category') || $purchaseIntersect) {
            $data4 = EwalletPurchaseHistory::query();
            $data4->select(['ewallet_purchase_histories.id', 'user_id', 'reference_id as refference_id', 'amount', 'balance', 'amount_type', DB::raw("type as debit"), 'date_added', 'ewallet_purchase_histories.created_at as createdAt', 'ewallet_purchase_histories.updated_at as updatedAt', 'ewallet_type', DB::raw("NULL as purchase_wallet"), DB::raw("NULL as transaction_fee")]);

            if ($request->has('fromDate') && $request->has('toDate')) {
                $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
                $toDate = Carbon::parse($request->toDate)->format('Y-m-d');
                $data4->whereBetween('date_added', [$fromDate, $toDate]);
            }

            if ($request->has('users') && count($request->users) > 0) {
                $data4->whereHas('user', fn ($user) => $user->whereIn('id', [...$request->users]));
            } else {
                // $data4->whereHas('user', fn ($user) => $user->where('type', '!=', 'employee'));
                $data4->join('users', 'users.id', '=', 'ewallet_purchase_histories.user_id')
                    ->whereIn('users.user_type', ['admin', 'user']);
            }

            if ($request->has('type')) {
                $data4->whereIn('type', $request->type);
            }
            if ($request->has('category')) {
                $data4->whereIn('amount_type', $request->category);
            }
            $query->push($data4);
            $count += $data4->count();
        }

        $finalQuery = $query->shift();
        foreach ($query as $q) {
            $finalQuery->unionAll($q);
        }
        if ($count < 80000) {
            $finalQuery->orderBy('date_added', 'desc');
        }
        $data = $finalQuery->with(['user:id,username', 'user.userDetails'])
            ->skip($request->start)
            ->take(10)->get();
        $ewalletTrns = [];

        foreach ($data as $i => $item) {
            $ewalletTrns[$i]['username'] = '<div class="d-flex"><img class="ht-30 img-transaction" src="' . asset('/assets/images/users/avatar-1.jpg') . '"><div class="transaction-user">' . $item->user->userDetails->name . ' ' . $item->user->userDetails->second_name . ' ' . '<h5>' . $item->user->username . '</h5><span></div></div>';

            $amountType     =  $item->amount_type;
            if ($item->ewallet_type == "commission") {
                $amountType = $item->amount_type;
            } elseif ($item->ewallet_type == "fund_transfer") {
                if ($item->amount_type == 'user_credit') $amountType = 'fund_credit_user';
                if ($item->amount_type == 'user_debit') $amountType = 'fund_debit_user';
                if ($item->amount_type == 'admin_user_credit') $amountType = 'admin_fund_credit_user';
                if ($item->amount_type == 'admin_user_debit') $amountType = 'admin_fund_debit_user';
                if ($item->amount_type == 'payout_delete') $amountType = 'payout_delete';
                if ($item->amount_type == 'payout_cancel') $amountType = 'payout_cancel';

            } elseif ($item->ewallet_type == "fund_transfer_fee") {
                $amountType =  'transaction_fee';
            } else {
                $amountType = $item->amount_type;
            }
            $ewalletTrns[$i]['amount_type'] = trans('ewallet.' . $amountType);

            $badge = '';
            $amount = $item->amount;
            if ($item->ewallet_type == "fund_transfer") {
                if ($item->amount_type == 'user_credit') $badge = 'credit';
                if ($item->amount_type == 'user_debit') $badge = 'debit';
                if ($item->amount_type == 'admin_user_credit') $badge = 'credit';
                if ($item->amount_type == 'admin_user_debit') $badge = 'debit';
                if ($item->amount_type == 'admin_credit') $badge = 'credit';
                if ($item->amount_type == 'admin_debit') $badge = 'debit';
                if ($item->amount_type == 'payout_delete') $badge = 'credit';
                if ($item->amount_type == 'payout_cancel') $badge = 'credit';
                if ($item->amount_type == 'pin_purchase') $badge = 'debit';
            } elseif ($item->ewallet_type == "commission") {
                $badge = 'credit';
            } elseif ($item->ewallet_type == "fund_transfer_fee") {
                $badge = 'debit';
                $amount = $item->transaction_fee;
            } else {
                $badge = $item->type;
            }
            $ewalletTrns[$i]['amount'] = '<span class="badge-' . $badge . '">' . $currency . ' ' . formatCurrency($amount) . '</span>';
            $ewalletTrns[$i]['date'] =  Carbon::parse($item->createdAt)->format('M d, Y, h:i A');
        }

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $ewalletTrns
        ]);
    }


    public function ewalletBalance(Request $request)
    {
        $currency       = currencySymbol();
        $data           = User::query();

        if ($request->has('user') && count($request->user) > 0) {
            $data->where(function ($query) use ($request) {
                foreach ($request->user as $user) {
                    $query->orWhere('id', $user);
                }
            });
        }
        $ewalletBalance = $data->where('user_type', '!=', 'employee')->with('userDetail', 'userBalance')->whereHas('userBalance', fn ($q) => $q->where('balance_amount', '>', 0));

        return Datatables::of($ewalletBalance)
            ->addColumn('member', function ($ewalletBalance) {
                return '<div class="d-flex"><img class="ht-30 img-transaction" src="' . asset('/assets/images/users/avatar-1.jpg') . '"><div class="transaction-user"><h5>' . $ewalletBalance->username . '(
                                ' . $ewalletBalance->userDetail->name . ')
                                </h5><span>' . $ewalletBalance->username . '</span></div></div>';
            })
            ->addColumn('balance', fn (User $user) => '<span class="bg">' . $currency . formatCurrency($user->userBalance->balance_amount) . '</span>')
            ->rawColumns(['member', 'balance'])
            ->make(true);
    }

    public function purchaseWallet(Request $request)
    {
        $data = Purchasewallethistory::query();
        $currency = currencySymbol();
        if ($request->has('users') && count($request->users) > 0) {
            $data->whereHas('user', fn ($user) => $user->whereIn('id', [...$request->users]));
        } else {
            $data->whereHas('user', fn ($user) => $user->where('id', auth()->user()->id));
        }

        $purchaseWalletHistories = $data->orderBy('id', 'desc')->with('user', 'fromUser');

        $fromUserAmountTypes = collect([
            'referral',
            'level_commission',
            'repurchase_level_commission',
            'upgrade_level_commission',
            'xup_commission',
            'xup_repurchase_level_commission',
            'xup_upgrade_level_commission',
            'sales_commission',
        ]);

        $moduleStatus = $this->moduleStatus();

        return Datatables::of($purchaseWalletHistories)
            ->addColumn('description', function ($item) use ($moduleStatus, $fromUserAmountTypes) {
                $description = '';
                if ($item->amount_type == 'donation') {
                    $description = __('ewallet.donation_credit' . $item->fromuser->username);
                    if ($item->type == 'debit') {
                        $description = __('ewallet.donation_debit' . $item->fromUser->username);
                    }
                } elseif ($item->amount_type == 'board_commission' && $moduleStatus->table_status) {
                    $description = __('ewallet.table_commission');
                } elseif ($item->amount_type == 'repurchase') {
                    $description = __('ewallet.deducted_for_repurchase_by', ['username' => $item->fromUser->username]);
                } elseif ($item->amount_type == 'purchase_donation') {
                    $description = __('ewallet.purchase_donation_from', ['username' => $item->fromUser->username]);
                } elseif ($fromUserAmountTypes->contains($item->amount_type)) {
                    $description = __("ewallet.{$item->amount_type}_from", ['username' => $item->fromUser->username]);
                } else {
                    $description = __("ewallet.{$item->amount_type}");
                }

                return $description;
            })
            ->editColumn('amount', fn ($item) => '<span class="amount-span">' . $currency . ' ' . formatCurrency($item->purchase_wallet) . '</span>')
            ->addColumn('balance', fn ($item) => '<span class="balance-span">' . $currency . ' ' . formatCurrency($item->balance) . '</span>')
            ->editColumn('date', fn ($item) => Carbon::parse($item->date)->format('M d, Y'))
            ->rawColumns(['description', 'amount', 'balance', 'date'])
            ->make(true);
    }

    public function ewalletStatement(Request $request)
    {
        if ($request->has('user') && $request->user != '') {
            $currentUser    = User::with('userBalance')->find($request->user);
        } else {
            $currentUser    = auth()->user()->load('userBalance');
        }
        $query      = collect([]);
        $count      = 0;
        $user       = $currentUser;

        // fund transfer details
        $data = EwalletTransferHistory::query();
        $data->whereHas('user', fn ($user) => $user->where('id', $currentUser->id));
        $data->leftJoin('fund_transfer_details', 'fund_transfer_details.id', '=', 'ewallet_transfer_histories.fund_transfer_id');
        $data->leftJoin('users as from', 'from.id', '=', 'fund_transfer_details.from_id');
        $data->select(['ewallet_transfer_histories.id', 'ewallet_transfer_histories.user_id', 'ewallet_transfer_histories.fund_transfer_id', 'ewallet_transfer_histories.amount', 'ewallet_transfer_histories.balance', 'ewallet_transfer_histories.amount_type', 'ewallet_transfer_histories.type', 'ewallet_transfer_histories.date_added', 'ewallet_transfer_histories.created_at', 'ewallet_transfer_histories.updated_at', DB::raw("'fund_transfer' as ewallet_type, NULL as purchase_wallet"), 'transaction_fee', 'from.username as from_user']);
        $query->push($data);
        $count += $data->count();

        // comission history
        $data1 = EwalletCommissionHistory::query();
        $data1->whereHas('user', fn ($user) => $user->where('id', $currentUser->id));
        $data1->leftJoin('users as from', 'from.id', '=', 'ewallet_commission_histories.from_id');
        $data1->select(['ewallet_commission_histories.id', 'ewallet_commission_histories.user_id', 'ewallet_commission_histories.leg_amount_id as refference_id', 'ewallet_commission_histories.amount', 'ewallet_commission_histories.balance', 'ewallet_commission_histories.amount_type', DB::raw("'credit' as type"), 'ewallet_commission_histories.date_added', 'ewallet_commission_histories.created_at', 'ewallet_commission_histories.updated_at', DB::raw("'commission' as ewallet_type"), 'purchase_wallet', DB::raw("NULL as transaction_fee"), 'from.username as from_user']);
        $query->push($data1);
        $count += $data1->count();

        // ewallet purchases
        $data2 = EwalletPurchaseHistory::query();
        $data2->whereHas('user', fn ($user) => $user->where('id', $currentUser->id));
        $data2->select(['ewallet_purchase_histories.id', 'user_id', 'reference_id as refference_id', 'amount', 'balance', 'amount_type', 'type', 'date_added', 'ewallet_purchase_histories.created_at', 'ewallet_purchase_histories.updated_at', 'ewallet_type', DB::raw("NULL as purchase_wallet"), DB::raw("NULL as transaction_fee"), DB::raw("NULL as from_user")]);
        $query->push($data2);
        $count += $data2->count();
        $finalQuery = $query->shift();
        foreach ($query as $q) {
            $finalQuery->union($q);
        }

        $ewalletStatement   = $finalQuery->orderBy('date_added', 'DESC')->with(['user:id,username', 'user.userDetails', 'user.userBalance']);

        $moduleStatus       = $this->moduleStatus();
        $currency           = currencySymbol();
        $admin              = User::GetAdmin();

        return Datatables::of($ewalletStatement)
            ->addColumn('description', function ($item) use ($moduleStatus, $user, $admin) {
                return $this->serviceClass->walletStatementDescription($item, $moduleStatus, $user, $admin);
            })
            ->editColumn('amount', function ($item) use ($currency, $user) {
                if ($item->ewallet_type == 'fund_transfer') {
                    if ($item->amount_type == "user_credit") {
                        $class = ($user->id == $item->user_id)
                            ? "credit"
                            : "debit";
                        $amount = ($user->id == $item->user_id)
                            ? $item->amount
                            : $item->amount + $item->transaction_fee;
                    } elseif ($item->amount_type == "user_debit") {
                        $class = ($user->id == $item->user_id)
                            ? "credit"
                            : "debit";
                        if ($item->type == "credit") {
                            $amount = ($user->id == $item->user_id)
                            ? $item->amount
                            : $item->amount + $item->transaction_fee;
                        } else {
                            $amount = ($user->id == $item->user_id)
                                ? $item->amount + $item->transaction_fee
                                : $item->amount;
                        }
                    } elseif ($item->amount_type == "payout_delete") {
                        $class = ($user->id == $item->user_id)
                            ? "credit"
                            : "debit";
                        if ($item->type == "credit") {
                            $amount = ($user->id == $item->user_id)
                            ? $item->amount
                            : $item->amount + $item->transaction_fee;
                        }
                    } else {
                        $class = ($item->type == 'credit')
                            ? "credit"
                            : "debit";
                        $amount = ($user->id == $item->user_id)
                            ? $item->amount
                            : $item->amount;
                    }
                } else {
                    $class = ($item->type == 'credit')
                        ? "credit"
                        : "debit";
                    $amount = $item->amount - $item->purchase_wallet;
                }
                return "<span class='amount-span badge-{$class}'>{$currency} &nbsp;" . formatCurrency($amount) . '</span>';
            })
            ->addColumn('balance', function ($item) use ($currency, $user) {
                if ($item->ewallet_type == 'fund_transfer') {
                    if ($item->amount_type == "user_credit") {
                        // $balance = $item->from_balance;
                        $balance = ($user->id == $item->user_id)
                            ? $item->balance
                            : $item->from_balance;
                    } elseif ($item->amount_type == "user_debit") {
                        $balance = ($user->id == $item->user_id)
                            ? $item->balance
                            : $item->from_balance;
                    } else {
                        $balance = $item->balance;
                    }
                } else {
                    $balance = $item->balance;
                }
                return "<span class='badge-balance'>{$currency} &nbsp;" . formatCurrency($balance) . "</span>";
            })
            ->editColumn('created_at', fn ($item) => Carbon::parse($item->created_at)->format('M d, Y h:i A'))
            ->rawColumns(['description', 'amount', 'balance', 'date', 'created_at'])
            ->make(true);
    }

    public function userEarnings(Request $request)
    {
        $currency = currencySymbol();
        $data = LegAmount::query();
        if ($request->has('users') && count($request->users) > 0) {
            $data->whereIn('user_id', $request->users);
        }
        if ($request->has('category')) {
            $data->whereIn('amount_type', $request->category);
        }
        if ($request->has('fromDate') && $request->has('toDate')) {
            $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
            $toDate = Carbon::parse($request->toDate)->format('Y-m-d');
            $data->whereBetween('date_of_submission', [$fromDate, $toDate]);
        }

        $userEarnings = $data->with('userDetails', 'user', 'fromUser.userDetail');
        $moduleStatus = $this->moduleStatus();
        // dd($userEarnings);
        return DataTables::of($userEarnings)
            ->addColumn('category', function ($data) use ($moduleStatus) {
                if ($data->amount_type == 'board_commission' && $moduleStatus->mlm_plan == 'Board' && $moduleStatus->table_status) {
                    return __('ewallet.table_commission');
                }
                if (
                    $data->amount_type == 'level_commission' ||
                    $data->amount_type == 'repurchase_level_commission' ||
                    $data->amount_type == 'upgrade_level_commission' ||
                    $data->amount_type == 'xup_commission' ||
                    $data->amount_type == 'xup_repurchase_level_commission' ||
                    $data->amount_type == 'xup_upgrade_level_commission' ||
                    $data->amount_type == 'matching_bonus' ||
                    $data->amount_type == 'matching_bonus_purchase' ||
                    $data->amount_type == 'matching_bonus_upgrade' ||
                    $data->amount_type == 'sales_commission'
                ) {
                    return __('ewallet.' . $data->amount_type . '_received_from_from_level', ['username' => Str::upper($data->fromUser->username), 'level' => $data->user_level]);
                } elseif ($data->amount_type == 'referral') {
                    return Str::ucfirst(__('ewallet.commission_received_from', ['commission' => Str::ucfirst($data->amount_type), 'username' => Str::upper($data->fromUser->username)]));
                } else {
                    return Str::ucfirst(__("ewallet.$data->amount_type"));
                }
            })
            ->addColumn('total_amount', fn (LegAmount $data)  => $currency . ' ' . formatCurrency($data->total_amount))
            ->editColumn('tax', fn (LegAmount $data) => "<span class=''>$currency " . formatCurrency($data->tds) . "</span>")
            ->editColumn('service_charge', fn (LegAmount $data) => $currency . ' ' . formatCurrency($data->service_charge))
            ->addColumn('amount_payable', fn (LegAmount $data) => "<span>$currency " . formatCurrency($data->amount_payable) . "</span>")
            ->editcolumn('date_of_submission', fn (LegAmount $data) => Carbon::parse($data->date_of_submission)->format('M d, Y, h:iA'))
            ->rawColumns(['total_amount', 'tax', 'service_charge', 'amount_payable', 'transaction_date'])
            ->make(true);
    }

    public function singleUserEarnings(Request $request)
    {
        $userEarnings = LegAmount::where('user_id', $request->id)->get();
        $module_status = $this->moduleStatus();
        $currency = currencySymbol();
        $data = [];
        foreach ($userEarnings as $item) {
            if ($item->amount_type == 'board_commission' && $module_status->mlm_plan == 'Board' && $module_status->table_status) {
                $itemAmountType = 'table_commission';
            }

            if ($item->amount_type == 'level_commission' || $item->amount_type == 'repurchase_level_commission' || $item->amount_type == 'upgrade_level_commission' || $item->amount_type == 'xup_commission' || $item->amount_type == 'xup_repurchase_level_commission' || $item->amount_type == 'xup_upgrade_level_commission' || $item->amount_type == 'matching_bonus' || $item->amount_type == 'matching_bonus_purchase' || $item->amount_type == 'matching_bonus_upgrade' || $item->amount_type == 'sales_commission') {
                $itemAmountType = $item->amount_type . ' ' . 'received from' . ' ' . $item->user->username . ' ' . 'from level' . ' ' . $item->user_level;
            } elseif ($item->amount_type == 'referral') {
                $itemAmountType = $item->amount_type . ' ' . 'received from' . ' ' . $item->user->username;
            } else {
                $itemAmountType = $item->amount_type;
            }

            $amount_payable = $item->total_amount + ($item->service_charge + $item->tds);
            $data[] = [
                'category' => $itemAmountType,
                'amount' => $currency . ' ' . formatCurrency(number_format($amount_payable, 2)),
                'tax' => $item->tds,
                'service_charge' => $item->service_charge,
                'amount_payable' => $item->total_amount,
                'transaction_date' => date('F j, Y, g:i a', strtotime($item->date_of_submission)),
            ];
        }

        $earningsCategories = [];
        $bonusList = $this->getEnabledBonusList();
        array_push($earningsCategories, $bonusList);
        $view = view('report.ajax.user-earnings', compact('data', 'earningsCategories'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ], 200);
    }

    public function Alluser(Request $request)
    {
        if ($request->has('search')) {
            $string = $request->search;
            $users = User::whereIn("user_type", ["user", "admin"])->where('username', 'like', '%' . $string . '%')->select('id', 'username')->paginate(10);

            return response()->json([
                'status' => true,
                'data' => $users,

            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No user found',
            ]);
        }
    }

    public function fundTransfer(FundTransferRequest $request)
    {
        $validatedData = $request->validated();
        $moduleStatus = $this->moduleStatus();
        $transferFee = $this->configuration()->trans_fee;
        $amountAfterFee = defaultCurrency($validatedData['amount'] + $transferFee);
        $fromUser = User::with('userBalance', 'transPassword')->findOrFail($validatedData['transfer_from']);
        $toUser = User::with('userBalance', 'ewalletTransfer')->findOrFail($validatedData['transfer_to']);

        if ($amountAfterFee > $fromUser->userBalance->balance_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Low balance.',
            ], 403);
        }
        if (!Hash::check($validatedData['transaction_password'], $fromUser->transPassword->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect transaction password.',
            ], 403);
        }
        DB::beginTransaction();
        try {
            $transactionId = generateTransactionNumber();
            $transaction = Transaction::create(['transaction_id' => $transactionId]);
            $type = '';

            if ($type != '') {
                $transType = ($type == 'admin_debit') ? 'debit' : 'credit';
                $amountType = $type;
            } else {
                $transType = 'credit';
                $amountType = 'admin_user_credit';
            }
            $transDetails = $this->serviceClass->insertTransactionDetails($fromUser, $toUser, $validatedData, defaultCurrency($transferFee), $transaction, $moduleStatus, $type);
            $this->serviceClass->addToEwalletTransferHistory($moduleStatus, $fromUser, $toUser, $transDetails->id, defaultCurrency($validatedData['amount']), $amountType, $transType, $transaction->id, defaultCurrency($transferFee), $validatedData['notes']);

            $this->serviceClass->updateFromUserBalance($fromUser, $amountAfterFee);
            $this->serviceClass->updateToUserBalance($toUser, defaultCurrency($validatedData['amount']));
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => true,
                'message' => $th->getMessage(),
            ], 404);
            throw $th;
        }
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Fund transfer successfull',
        ]);
    }

    public function fundCredit(FundCreditRequest $request)
    {
        $validatedData = $request->validated();

        $moduleStatus = $this->moduleStatus();
        if (auth()->user()->user_type == 'employee') {
            $fromUser = User::GetAdmin();
        } else {
            $fromUser = auth()->user();
        }
        $toUser = User::with('userBalance', 'ewalletTransfer')->findOrFail($validatedData['username']);
        DB::beginTransaction();
        // try {
            $transactionId = generateTransactionNumber();
            $transaction = Transaction::create(['transaction_id' => $transactionId]);
            $type = 'admin_credit';
            $transferFee = 0;
            $walletType = 'fund_transfer';

            if ($type != '') {
                $transType = ($type == 'admin_debit') ? 'debit' : 'credit';
                $amountType = $type;
            } else {
                $transType = 'credit';
                $amountType = 'user_credit';
            }
            $transDetails = $this->serviceClass->insertTransactionDetails($fromUser, $toUser, $validatedData, defaultCurrency($transferFee), $transaction, $moduleStatus, $type);
            $this->serviceClass->addToEwalletTransferHistory($moduleStatus, $fromUser, $toUser, $transDetails->id, defaultCurrency($validatedData['amount']), $amountType, $transType, $transaction->id, defaultCurrency($transferFee), $validatedData['notes'], $walletType);

            $this->serviceClass->updateToUserBalance($toUser, defaultCurrency($validatedData['amount']));
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     return response()->json([
        //         'status' => true,
        //         'message' => $th->getMessage(),
        //     ], 404);
        //     throw $th;
        // }
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Fund Credited successfully',
        ]);
    }

    public function fundDebit(FundCreditRequest $request)
    {
        $validatedData = $request->validated();

        $moduleStatus = $this->moduleStatus();
        if (auth()->user()->user_type == 'employee') {
            $fromUser = User::GetAdmin();
        } else {
            $fromUser = auth()->user();
        }
        $toUser = User::with('userBalance', 'ewalletTransfer')->findOrFail($validatedData['username']);

        if (defaultCurrency($validatedData['amount']) > $toUser->userBalance->balance_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Low balance.',
            ], 403);
        }
        DB::beginTransaction();
        try {
            $transactionId = generateTransactionNumber();
            $transaction = Transaction::create(['transaction_id' => $transactionId]);
            $type = 'admin_debit';
            $transferFee = 0;
            $walletType = 'fund_transfer';

            if ($type != '') {
                $transType = ($type == 'admin_debit') ? 'debit' : 'credit';
                $amountType = $type;
            } else {
                $transType = 'credit';
                $amountType = 'user_credit';
            }
            $transDetails = $this->serviceClass->insertTransactionDetails($fromUser, $toUser, $validatedData, defaultCurrency($transferFee), $transaction, $moduleStatus, $type);
            $this->serviceClass->addToEwalletTransferHistory($moduleStatus, $fromUser, $toUser, $transDetails->id, defaultCurrency($validatedData['amount']), $amountType, $transType, $transaction->id, defaultCurrency($transferFee), $validatedData['notes']);

            $this->serviceClass->deductUserBalance($toUser, defaultCurrency($validatedData['amount']));
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => true,
                'message' => $th->getMessage(),
            ], 404);
            throw $th;
        }
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Fund Debited successfully',
        ]);
    }

    public function showEwalletBalance($id)
    {
        $userBalance = UserBalanceAmount::where('user_id', $id)->first();

        return response()->json([
            'status' => true,
            'data' => formatCurrency($userBalance->balance_amount),
        ]);
    }

    public function checkEwalletAvailability(Request $request)
    {
        $request->validate([
            'transaction_username' => 'required|exists:users,username',
            'tranPassword' => 'required',
        ]);
        $username = $request->transaction_username;
        $requestUser = User::where('user_type', '!=', 'employee')->where('username', $request->transaction_username)->with('userBalance', 'transPassword')->first();

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

    public function checkPurchaseWalletAvailability(Request $request)
    {
        $request->validate([
            'transaction_username' => 'required|exists:users,username',
            'tranPassword' => 'required',
        ]);
        $username = $request->transaction_username;
        $requestUser = User::where('user_type', '!=', 'employee')->where('username', $request->transaction_username)->with('userBalance', 'transPassword')->first();

        $password = $requestUser->transPassword->password;
        $passwordCheck = Hash::check($request->tranPassword, $password);

        if (!$passwordCheck) {
            throw ValidationException::withMessages([
                'tranPassword' => __('register.transaction_password_not_correct'),
            ]);
        }

        $purchaseWalletBalance = $requestUser->userBalance->purchase_wallet;
        $totalRegAmount = defaultCurrency($request->totalAmount);
        if ($purchaseWalletBalance < $totalRegAmount) {
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
}
