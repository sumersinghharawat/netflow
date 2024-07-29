<?php

namespace App\Services;

use App\Models\AmountPaid;
use App\Models\Fundtransferdetail;
use App\Models\LegAmount;
use App\Models\OcOrder;
use App\Models\Order;
use App\Models\User;
use App\Models\Packagevalidityextendhistory;
use App\Models\PayoutReleaseRequest;
use App\Models\TotalIncome;
use App\Models\UpgradesalesOrder;
use App\Models\UserRegistrationView;
use App\Models\UsersRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BusinessService
{
    public function getBusinessCategories($moduleStatus, $bonusList)
    {
        $categories = $this->incomeCategories($moduleStatus);
        $bonusCategories = $this->bonusCategories($bonusList);
        $bonusCategories->map(fn ($bonus) => $categories->push($bonus));
        $categories->push('paid', 'pending');

        return $categories;
    }

    public function totalIncome($moduleStatus, Request $request, $bonusList)
    {
        $grandTotal = 0;
        $userRegisteration = UsersRegistration::query();
        if ($request->fromDate || $request->toDate) {
            $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
            $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
            $userRegisteration->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $result['registerAmount'] = $userRegisteration->sum('product_amount') ?? 0;

        if ($moduleStatus->ecom_status) {
            $orderTypes = ['register', 'purchase'];
            if ($moduleStatus->package_upgrade) {
                $orderTypes[] = 'upgrade';
            }
            if ($moduleStatus->subscription_status) {
                $orderTypes[] = 'renewal';
            }
            $dbPrefix = config('database.connections.mysql.prefix');
            $query = DB::table('oc_order')
                ->join('oc_order_product', 'oc_order.order_id', '=', 'oc_order_product.order_id')
                ->where('oc_order.order_status_id', 5)
                ->selectRaw("sum({$dbPrefix}oc_order_product.total) as amount")
                ->first();
            $grandTotal += $query->amount ?? 0;
        } else {
            $result['joiningFee'] = $userRegisteration->sum('reg_amount') ?? 0;
            $grandTotal += $result['joiningFee'];

            if ($moduleStatus->product_status) {
                $grandTotal += $result['registerAmount'];
            }
            if ($moduleStatus->repurchase_status) {
                $order = Order::query();
                if ($request->fromDate || $request->toDate) {
                    $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
                    $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
                    $order->whereBetween('order_date', [$fromDate, $toDate]);
                }
                $result['orderAmount'] = $order->where('order_status', 'confirmed')->sum('total_amount') ?? 0;

                $grandTotal += $result['orderAmount'];
            }
            if ($moduleStatus->package_upgrade) {
                $upgradeSalesOrder = UpgradesalesOrder::query();
                if ($request->fromDate || $request->toDate) {
                    $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
                    $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
                    $upgradeSalesOrder->whereBetween('created_at', [$fromDate, $toDate]);
                }
                $result['upgradeAmount'] = $upgradeSalesOrder->sum('amount') ?? 0;
                $grandTotal += $result['upgradeAmount'];
            }
            if ($moduleStatus->subscription_status) {
                $validityextendHistory = Packagevalidityextendhistory::query();
                if ($request->fromDate || $request->toDate) {
                    $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
                    $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
                    $validityextendHistory->whereBetween('created_at', [$fromDate, $toDate]);
                }
                $result['renewalAmount'] = $validityextendHistory->sum('total_amount') ?? 0;
                $grandTotal += $result['renewalAmount'];
            }
        }

        $fundTransfer = Fundtransferdetail::query();

        if ($request->fromDate || $request->toDate) {
            $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
            $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
            $fundTransfer->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $result['fundTransferAmount'] = $fundTransfer->sum('trans_fee') ?? 0;
        $grandTotal += $result['fundTransferAmount'];

        $amountpaidDetails = AmountPaid::query();
        if ($request->fromDate || $request->toDate) {
            $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
            $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
            $amountpaidDetails->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $result['payoutFeeAmount'] = $amountpaidDetails->sum('payout_fee') ?? 0;
        $grandTotal += $result['payoutFeeAmount'];
        if ($request->label == 'All Time') {
            $result['serviceChargeAmount'] = TotalIncome::whereIn('amount_type', $bonusList)->sum('service_charge');
        } else {
            if ($request->fromDate || $request->toDate) {
                $legAmount = LegAmount::query();
                $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
                $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
                $legAmount->whereBetween('created_at', [$fromDate, $toDate]);
                $result['serviceChargeAmount'] = $legAmount->whereIn('amount_type', $bonusList)->sum('service_charge') ?? 0;
            }
        }
        $grandTotal += $result['serviceChargeAmount'] ?? 0;

        return $result;
    }

    public function totalBonus($moduleStatus, Request $request, $bonusList)
    {
        $combinedTypes = [
            'pin_purchase_delete' => 'pin_purchase_refund',
            'payout_inactive' => 'payout_delete',
            'withdrawal_cancel' => 'payout_delete',
            'repurchase_level_commission' => 'level_commission',
            'upgrade_level_commission' => 'level_commission',
            'xup_repurchase_level_commission' => 'xup_commission',
            'xup_upgrade_level_commission' => 'xup_commission',
            'repurchase_leg' => 'leg',
            'upgrade_leg' => 'leg',
            'matching_bonus_purchase' => 'matching_bonus',
            'matching_bonus_upgrade' => 'matching_bonus',
            'purchase_donation' => 'donation',
        ];
        if ($request->label == 'All Time') {
            $result['serviceChargeAmount'] = TotalIncome::whereIn('amount_type', $bonusList)->sum('service_charge') ?? 0;
        } else {
            $legAmountToatl = LegAmount::query();
            if ($request->fromDate || $request->toDate) {
                $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
                $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
                $legAmount = $legAmountToatl->whereBetween('created_at', [$fromDate, $toDate]);
                $result['serviceChargeAmount']    = $legAmountToatl->whereIn('amount_type', $bonusList)->sum('service_charge') ?? 0;
            }
        }
        $availableBonus = $bonusList->filter(fn ($bonus) => !Arr::exists($combinedTypes, $bonus));
        $bonus = $availableBonus->mapWithKeys(fn ($bonus) => [$bonus => 0]);
        foreach ($bonusList as $bonus) {
            $result[$bonus] = 0;
            if (in_array($bonus, array_keys($combinedTypes))) {
                $walletDetails[$combinedTypes[$bonus]] = $result[$bonus];
                unset($result[$bonus]);
            }
        }
        if ($request->label == 'All Time') {
            $legAmountDetails = TotalIncome::whereIn('amount_type', $bonusList)->select('amount_type', DB::raw('SUM(amount_payable) As amount_payable'))
                ->groupBy('amount_type')->get();
            foreach ($legAmountDetails as $details) {
                if (in_array($details['amount_type'], array_keys($combinedTypes))) {
                    $details['amount_type'] = $combinedTypes[$details['amount_type']];
                }
                $result[$details['amount_type']] += $details['amount_payable'];
            }
        } else {
            $legAmountDetails = $legAmount->whereIn('amount_type', $bonusList)->select('amount_type', DB::raw('SUM(amount_payable) As amount_payable'))
                ->groupBy('amount_type')->orderBY('amount_type', 'ASC')->chunk(1000, function ($qry) use ($combinedTypes, &$result) {
                    foreach ($qry as $details) {
                        if (in_array($details['amount_type'], array_keys($combinedTypes))) {
                            $details['amount_type'] = $combinedTypes[$details['amount_type']];
                        }
                        $result[$details['amount_type']] += $details['amount_payable'];
                    }
                });
        }
        unset($result['serviceChargeAmount']); // because bonus calculation total amount is not matching and suggested by tester.
        return $result;
    }

    public function totalPending($moduleStatus, Request $request, $bonusList)
    {
        $payoutApprovedPending = AmountPaid::where('type', 'released')->where('status', '0')->where('payment_method', '=', 'bank')->sum('amount');
        $payoutRequestpendingDet = PayoutReleaseRequest::query();
        if ($request->fromDate || $request->toDate) {
            $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
            $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
            $payoutRequestpendingDet->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $payoutPending = $payoutRequestpendingDet->where('status', '0')->sum('balance_amount') ?? 0;

        return $payoutApprovedPending + $payoutPending;
    }

    public function totalPaid($moduleStatus, Request $request, $bonusList)
    {
        $amountpaidDetails = AmountPaid::query();
        if ($request->fromDate || $request->toDate) {
            $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
            $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
            $amountpaidDetails->whereBetween('created_at', [$fromDate, $toDate]);
        }

        return $amountpaidDetails->where('type', 'released')->where('status', '1')->sum('amount') ?? 0;
    }

    public function totalOverView($moduleStatus, $bonusList)
    {
        $walletTotal['income'] = 0;
        $walletTotal['bonus'] = 0;
        $walletTotal['paid'] = 0;
        $walletTotal['pending'] = 0;

        $userRegisteration = UserRegistrationView::first();
        $joiningFee = $userRegisteration->regAmount ?? 0;
        $packageAmount = $userRegisteration->productAmount ?? 0;

        if ($moduleStatus->ecom_status) {
            $orderTypes = ['register', 'purchase'];
            if ($moduleStatus->package_upgrade) {
                $orderTypes[] = 'upgrade';
            }
            if ($moduleStatus->subscription_status) {
                $orderTypes[] = 'renewal';
            }
            $dbPrefix = config('database.connections.mysql.prefix');
            $query = DB::table('oc_order')
                ->join('oc_order_product', 'oc_order.order_id', '=', 'oc_order_product.order_id')
                ->where('oc_order.order_status_id', 5)
                ->selectRaw("sum({$dbPrefix}oc_order_product.total) as amount")
                ->first();
            $walletTotal['income'] += $query->amount ?? 0;
        } else {
            $walletTotal['income'] += $joiningFee;
            if ($moduleStatus->product_status) {
                $walletTotal['income'] += $packageAmount;
            }
            if ($moduleStatus->repurchase_status) {
                $order = Order::query();
                $orderAmount = $order->where('order_status', 'confirmed')->sum('total_amount') ?? 0;
                $walletTotal['income'] += $orderAmount;
            }
            if ($moduleStatus->package_upgrade) {
                $upgradeSalesOrder = UpgradesalesOrder::query();
                $packageAmount = $upgradeSalesOrder->sum('amount') ?? 0;
                $walletTotal['income'] += $packageAmount;
            }
            if ($moduleStatus->subscription_status) {
                $validityextendHistory = Packagevalidityextendhistory::query();
                $renewalAmount = $validityextendHistory->sum('total_amount') ?? 0;
                $walletTotal['income'] += $renewalAmount;
            }
        }

        $fundTransfer = Fundtransferdetail::query();
        $fundTransferAmount = $fundTransfer->sum('trans_fee') ?? 0;
        $walletTotal['income'] += $fundTransferAmount;

        $amountpaidDetails = AmountPaid::query();
        $payoutFeeAmount = $amountpaidDetails->sum('payout_fee') ?? 0;
        $walletTotal['income'] += $payoutFeeAmount;

        $enabledBonusList = $bonusList;

        // $legAmount = LegAmount::query();
        // $serviceChargeAmount = $legAmount->select(DB::raw('SUM(service_charge) as service_charge'), DB::raw('SUM(amount_payable) as amount_payable'))
        $serviceChargeAmount = TotalIncome::select(DB::raw('SUM(service_charge) as service_charge'), DB::raw('SUM(amount_payable) as amount_payable'))->whereIn('amount_type', $enabledBonusList)->first();

        $walletTotal['income'] += $serviceChargeAmount->service_charge ?? 0;
        $walletTotal['bonus'] += $serviceChargeAmount->amount_payable ?? 0;

        $amountpaidDetails = AmountPaid::query();
        $payoutApprovedPaidAmount = $amountpaidDetails->where('type', 'released')->where('status', '1')->sum('amount') ?? 0;
        $walletTotal['paid'] += $payoutApprovedPaidAmount;

        $payoutApprovedPending = AmountPaid::where('type', 'released')->where('status', '0')->where('payment_method', '=', 'bank')->sum('amount');

        $payoutRequestpendingDet = PayoutReleaseRequest::query();
        $payoutRequestsPending = $payoutRequestpendingDet->where('status', '0')->sum('balance_amount');

        $walletTotal['pending'] += ($payoutRequestsPending + $payoutApprovedPending);

        return $walletTotal;
    }

    public function incomeCategories($moduleStatus)
    {
        $categories = collect(['fund_transfer_fee', 'commission_charge', 'payout_fee']);
        if ($moduleStatus->ecom_status) {
            $categories->push('register', 'purchase');
        } else {
            $categories->push('joining_fee');
            if ($moduleStatus->product_status) {
                $categories->push('register');
            }
            if ($moduleStatus->repurchase_status) {
                $categories->push('purchase');
            }
        }
        if ($moduleStatus->package_upgrade) {
            $categories->push('upgrade');
        }
        if ($moduleStatus->subscription_status) {
            $categories->push('renewal');
        }

        return $categories;
    }

    public function bonusCategories($bonusList)
    {
        $bonusList = $bonusList->diff(['purchase_donation', 'repurchase_level_commission', 'upgrade_level_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission', 'repurchase_leg', 'upgrade_leg', 'matching_bonus_purchase', 'matching_bonus_upgrade']);

        return $bonusList;
    }

    public function getBusinessTransactions($users, $type, $categories, $fromDate, $toDate, $moduleStatus, $bonusList, $filter, $currencySymbol)
    {
        if ($categories->isEmpty()) {
            $categories = $this->getBusinessCategories($moduleStatus, $bonusList);
        }

        $incomeCategories = $this->incomeCategories($moduleStatus);
        $bonusCategories = $this->bonusCategories($bonusList);
        $bonusCategories = $bonusList;
        $query = collect([]);
        $count = 0;
        if (in_array('income', $type) && !$categories->intersect($incomeCategories)->isEmpty()) {
            if ($moduleStatus->ecom_status) {
                $dbPrefix = config('database.connections.mysql.prefix');
                $orderTypes = $categories->intersect(['register', 'purchase', 'upgrade', 'renewal']);
                if (!empty($orderTypes)) {
                    $ocOrders = OcOrder::join('oc_order_product', 'oc_order.order_id', '=', 'oc_order_product.order_id')
                        ->join('users', 'users.ecom_customer_ref_id', '=', 'oc_order.customer_id')
                        ->whereIn('oc_order.order_type', $orderTypes)
                        ->where('oc_order.order_status_id', 5)
                        ->select(
                            DB::raw("SUM({$dbPrefix}oc_order_product.total) as amount"),
                            'users.id as user_id',
                            'oc_order.date_added as date',
                            'oc_order.order_type as amount_type',
                            DB::raw("'income' as type")
                        );
                    if (!empty($user_id)) {
                        $ocOrders->whereIn("users.id", $users);
                    }
                    if ($fromDate) {
                        $ocOrders->where('oc_order.date_added', '>=', $fromDate);
                    }
                    if ($toDate) {
                        $ocOrders->where('oc_order.date_added', '<=', $toDate);
                    }
                    $ocOrders->where("oc_order_product.total", '>', 0);

                    $ocOrders->groupBy('oc_order.order_type', 'oc_order.order_id', 'users.id');
                    $count += $ocOrders->count();
                    $query->push($ocOrders);
                }
            } else {
                if ($categories->contains('joining_fee')) {
                    $joiningFee = UsersRegistration::select('product_amount as amount', 'user_id', 'created_at as date', DB::raw("'joining_fee' as amount_type"), DB::raw("'income' as type"));
                    if (!empty($users)) {
                        $joiningFee->whereIn('user_id', $users);
                    }
                    if ($fromDate) {
                        $joiningFee->where('created_at', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
                    }
                    if ($toDate) {
                        $joiningFee->where('created_at', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
                    }
                    $joiningFee->where('product_amount', '>', 0);
                    $count += $joiningFee->count();
                    $query->push($joiningFee);
                }
                if ($moduleStatus->product_status && $categories->contains('register')) {
                    $register = UsersRegistration::select('reg_amount as amount', 'user_id', 'created_at as date', DB::raw("'register' as amount_type"), DB::raw("'income' as type"));
                    if (!empty($users)) {
                        $register->whereIn('user_id', $users);
                    }
                    if ($fromDate) {
                        $register->where('created_at', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
                    }
                    if ($toDate) {
                        $register->where('created_at', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
                    }
                    $register->where('reg_amount', '>', 0);

                    $count += $register->count();
                    $query->push($register);
                }
                if ($moduleStatus->repurchase_status && $categories->contains('purchase')) {
                    $purchase = Order::select('total_amount as amount', 'user_id', 'created_at as date', DB::raw("'purchase' as amount_type"), DB::raw("'income' as type"));
                    if (!empty($users)) {
                        $purchase->whereIn('user_id', $users);
                    }
                    if ($fromDate) {
                        $purchase->where('created_at', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
                    }
                    if ($toDate) {
                        $purchase->where('created_at', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
                    }
                    $purchase->where('total_amount', '>', 0);

                    $count += $purchase->count();
                    $query->push($purchase);
                }
                if ($moduleStatus->repurchase_status && $categories->contains('upgrade')) {
                    $upgrade = UpgradesalesOrder::select('amount', 'user_id', 'created_at as date', DB::raw("'upgrade' as amount_type"), DB::raw("'income' as type"));
                    if (!empty($users)) {
                        $upgrade->whereIn('user_id', $users);
                    }
                    if ($fromDate) {
                        $upgrade->where('created_at', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
                    }
                    if ($toDate) {
                        $upgrade->where('created_at', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
                    }
                    $upgrade->where('amount', '>', 0);

                    $count += $upgrade->count();
                    $query->push($upgrade);
                }
                if ($moduleStatus->subscription_status && $categories->contains('renewal')) {
                    $subscription = Packagevalidityextendhistory::select('total_amount as amount', 'user_id', 'created_at as date', DB::raw("'renewal' as amount_type"), DB::raw("'income' as type"));
                    if (!empty($users)) {
                        $subscription->whereIn('user_id', $users);
                    }
                    if ($fromDate) {
                        $subscription->where('created_at', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
                    }
                    if ($toDate) {
                        $subscription->where('created_at', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
                    }
                    $subscription->where('total_amount', '>', 0);

                    $count += $subscription->count();
                    $query->push($subscription);
                }
            }
            if ($categories->contains('fund_transfer_fee')) {
                $fundTransfer = Fundtransferdetail::select('trans_fee as amount', 'from_id as user_id', 'created_at as date', DB::raw("'trans_fee' as amount_type"), DB::raw("'income' as type"));
                $fundTransfer->where('trans_fee', '>', 0)
                    ->where('amount_type', 'user_credit');
                if (!empty($users)) {
                    $fundTransfer->whereIn('from_id', $users);
                }
                if ($fromDate) {
                    $fundTransfer->where('created_at', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
                }
                if ($toDate) {
                    $fundTransfer->where('created_at', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
                }
                $fundTransfer->where('trans_fee', '>', 0);

                $count += $fundTransfer->count();
                $query->push($fundTransfer);
            }
            if ($categories->contains('payout_fee')) {
                $payout = AmountPaid::select('payout_fee as amount', 'user_id', 'date', DB::raw("'payout_fee' as amount_type"), DB::raw("'income' as type"));
                $payout->where('payout_fee', '>', 0);
                    // ->where('payout_fee', 'user_credit');
                if (!empty($users)) {
                    $payout->whereIn('user_id', $users);
                }
                if ($fromDate) {
                    $payout->where('date', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
                }
                if ($toDate) {
                    $payout->where('date', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
                }
                $payout->where('payout_fee', '>', 0);

                $count += $payout->count();
                $query->push($payout);
            }
            if ($categories->contains('commission_charge')) {
                $commissionCharge = LegAmount::select('service_charge as amount', 'user_id', 'date_of_submission as date', DB::raw("'commission_charge' as amount_type"), DB::raw("'income' as type"));
                $commissionCharge->whereIn('amount_type', $bonusCategories)
                    ->where('service_charge', '>', 0);
                if (!empty($users)) {
                    $commissionCharge->whereIn('user_id', $users);
                }
                if ($fromDate) {
                    $commissionCharge->where('date_of_submission', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
                }
                if ($toDate) {
                    $commissionCharge->where('date_of_submission', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
                }
                $commissionCharge->where('service_charge', '>', 0);

                $count += $commissionCharge->count();
                $query->push($commissionCharge);
            }
        }
        if (in_array('bonus', $type) && !$categories->intersect($bonusCategories)->isEmpty()) {
            $amounts = [
                'pin_purchase_refund' => ['pin_purchase_refund', 'pin_purchase_delete'],
                'payout_delete' => ['payout_delete', 'payout_inactive', 'withdrawal_cancel'],
                'level_commission' => ['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'],
                'xup_commission' => ['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'],
                'leg' => ['leg', 'repurchase_leg', 'upgrade_leg'],
                'matching_bonus' => ['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'],
                'donation' => ['donation', 'purchase_donation'],
                'fund_transfer' => ['user_credit', 'user_debit'],
            ];
            $amountTypes = [];
            foreach ($categories as $category) {
                if (isset($amounts[$category])) {
                    $amountTypes = [...$amountTypes, ...$amounts[$category]];
                } else {
                    $amountTypes = [...$amountTypes, $category];
                }
            }
            $legAmount = LegAmount::select('total_amount as amount', 'user_id', 'date_of_submission as date', 'amount_type', DB::raw("'bonus' as type"));
            $legAmount->whereIn('amount_type', $amountTypes);
            if (!empty($users)) {
                $legAmount->whereIn('user_id', $users);
            }
            if ($fromDate) {
                $legAmount->where('date_of_submission', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
            }
            if ($toDate) {
                $legAmount->where('date_of_submission', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
            }
            $legAmount->where('total_amount', '>', 0);

            $count += $legAmount->count();
            $query->push($legAmount);
        }
        if (in_array('paid', $type) && $categories->contains('paid')) {
            $amountPaid = AmountPaid::select('amount', 'user_id', 'date', DB::raw("'payout_approved_paid' as amount_type"), DB::raw("'paid' as type"));
            $amountPaid->where('type', 'released')->where('status', 1);
            if (!empty($users)) {
                $amountPaid->whereIn('user_id', $users);
            }
            if ($fromDate) {
                $amountPaid->where('date', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
            }
            if ($toDate) {
                $amountPaid->where('date', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
            }
            $amountPaid->where('amount', '>', 0);

            $count += $amountPaid->count();
            $query->push($amountPaid);
        }
        if (in_array('pending', $type) && $categories->contains('pending')) {
            $amountPaidPending = AmountPaid::select('amount', 'user_id', 'date', DB::raw("'payout_approved_pending' as amount_type"), DB::raw("'pending' as type"));
            $amountPaidPending->where('type', 'released')
                ->where('status', 0)
                ->whereHas('paymentMethod', function ($qry) {
                    $qry->where('slug', 'bank-transfer');
                });
            if (!empty($users)) {
                $amountPaidPending->whereIn('user_id', $users);
            }
            if ($fromDate) {
                $amountPaidPending->where('date', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
            }
            if ($toDate) {
                $amountPaidPending->where('date', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
            }
            $amountPaidPending->where('amount', '>', 0);

            $count += $amountPaidPending->count();
            $query->push($amountPaidPending);

            $payoutRequest = PayoutReleaseRequest::select('balance_amount as amount', 'user_id', 'created_at as date', DB::raw("'payout_requests_pending' as amount_type"), DB::raw("'pending' as type"));
            $payoutRequest->where('status', 0);
            if (!empty($users)) {
                $payoutRequest->whereIn('user_id', $users);
            }
            if ($fromDate) {
                $payoutRequest->where('created_at', '>=', Carbon::parse($fromDate)->format('Y-m-d h:i:s'));
            }
            if ($toDate) {
                $payoutRequest->where('created_at', '<=', Carbon::parse($toDate)->format('Y-m-d h:i:s'));
            }
            $payoutRequest->where('balance_amount', '>', 0);

            $count += $payoutRequest->count();
            $query->push($payoutRequest);
        }

        $finalQuery = $query->shift();
        foreach ($query as $q) {
            $finalQuery->unionAll($q);
        }
        $results = $finalQuery->with(['user:id,username'])
                                ->orderBy('date','desc')
                                ->skip($filter['start'])
                                ->take($filter['limit'])->get();

        $query->push($results);

        $data = [];
        foreach ($results as $key => $value) {
            $data[$key] = $value;
            $userid = User::find($value->user_id);
            $user_details = $userid->userDetails;
            $data[$key]->amount_type = trans('business.' . $value->amount_type);
            if ($value->type == 'income') {
                $amountWithSpan = "<span class='badge-income'>" . $currencySymbol . ' ' . formatCurrency($value->amount) . "</span>";
            } elseif ($value->type == 'bonus') {
                $amountWithSpan = "<span class='badge-bonus'>" . $currencySymbol . ' ' . formatCurrency($value->amount) . "</span>";
            } elseif ($value->type == 'pending') {
                $amountWithSpan = "<span class='badge-pending'>" . $currencySymbol . ' ' . formatCurrency($value->amount) . "</span>";
            } elseif ($value->type == 'paid') {
                $amountWithSpan = "<span class='badge-paid'>" . $currencySymbol . ' ' . formatCurrency($value->amount) . "</span>";
            }
            $data[$key]->amount      = $amountWithSpan;
            $data[$key]->full_name   = '<div class="transaction-user">' . $user_details->name . ' ' .$user_details->second_name .' <h5> ' . $userid->username . '<h5></div>';
            $data[$key]->date        = Carbon::parse($value->date)->format('d M Y h:iA');

        }
        return  ['data' => $data, 'count' => $count];
    }

    public function getEcomCustomerNames($user_id)
    {
        $fullname = DB::table('user_details')
            ->select(DB::raw('CONCAT(name, " ", second_name) as full_name'))
            ->where('user_id', $user_id)
            ->first();
        return $fullname->full_name ?? 'NA';
    }
}
