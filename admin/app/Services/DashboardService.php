<?php

namespace App\Services;

use App\Models\Treepath;
use DateTime;
use App\Models\User;
use App\Models\Order;
use App\Models\LegAmount;
use App\Models\AmountPaid;
use App\Models\UpgradesalesOrder;
use App\Models\UsersRegistration;
use App\Models\Fundtransferdetail;
use Illuminate\Support\Facades\DB;
use App\Models\PayoutReleaseRequest;
use App\Models\Packagevalidityextendhistory;
use App\Models\AggregateUserCommissionAndIncome;
use App\Http\Controllers\CoreInfController as coreInf;
use App\Models\OcOrder;
use App\Models\OCProduct;
use App\Models\Package;
use App\Models\Rank;
use App\Models\TotalIncome;
use App\Models\UserRegistrationView;
use App\Models\EwalletPurchaseHistory;
use App\Models\EwalletTransferHistory;

class DashboardService
{
    public function getWalletDetails($moduleStatus)
    {
        $walletDetails              = [];
        $coreInf                    = new coreInf;
        $walletTotal['income']      = 0;
        $walletTotal['bonus']       = 0;
        $walletTotal['paid']        = 0;
        $walletTotal['pending']     = 0;
        $compensation               = $coreInf->compensation();
        $ewalletService             = new EwalletService;
        $bonusList                  = $ewalletService->getEnabledBonuses($moduleStatus, $compensation);
        $sum                        = UserRegistrationView::first();
        $joiningFee                 = $sum->regAmount ?? 0;
        $packageAmount              = $sum->productAmount ?? 0;
        $registrationTotalAmount    = $sum->totalAmount ?? 0;
        $walletDetails['joining_fee'] = [
            'type' => 'income',
            'amount' => $joiningFee,
        ];
        if ($moduleStatus->product_status) {
            $walletDetails['register'] = [
                'type' => 'income',
                'amount' => $packageAmount,
            ];
        }
        if ($moduleStatus->ecom_status) {
            unset($walletDetails['joining_fee']);

            $walletDetails['register'] = [
                'type' => 'income',
                'amount' => $registrationTotalAmount, //need to be from opencart
            ];
        }

        if ($moduleStatus->ecom_status) {
            $from_date = date('Y-01-01 00:00:00', strtotime('-5 year'));
            $to_date = date('Y-m-d');
            $ocOrder = OcOrder::where('order_status_id', 5)->where('order_type', 'purchase');
            if ($from_date) {
                $ocOrder->where('date_added', '>=', $from_date);
            }
            if ($to_date) {
                $ocOrder->where('date_added', '>=', $to_date);
            }
            $ocOrder = $ocOrder->select(DB::raw('SUM(total) as total'))->first();
            $orderTotal = $ocOrder->total ?? 0;
            $purchase_amount = $ocOrder->total - $registrationTotalAmount;
            $wallet_details['repurchase'] = [
                'type' => 'income',
                'amount' => $purchase_amount
            ];
        } else {
            $walletTotal['income'] += $joiningFee;
            if ($moduleStatus->product_status) {
                $walletTotal['income'] += $packageAmount;
            }
            if ($moduleStatus->repurchase_status) {
                $order = Order::query();

                $orderAmount = $order->where('order_status', 'confirmed')->sum('total_amount') ?? 0;
                $walletDetails['purchase'] = [
                    'type' => 'income',
                    'amount' => $orderAmount,
                ];

                $walletTotal['income'] += $orderAmount;
            }
            if ($moduleStatus->package_upgrade) {
                $upgradeSalesOrder = UpgradesalesOrder::query();

                $packageAmount = $upgradeSalesOrder->sum('amount') ?? 0;
                $walletDetails['upgrade'] = [
                    'type' => 'income',
                    'amount' => $packageAmount,
                ];
                $walletTotal['income'] += $packageAmount;
            }
            if ($moduleStatus->subscription_status) {
                $validityextendHistory = Packagevalidityextendhistory::query();

                $renewalAmount = $validityextendHistory->sum('total_amount') ?? 0;
                $walletDetails['renewal'] = [
                    'type' => 'income',
                    'amount' => $renewalAmount,
                ];
                $walletTotal['income'] += $renewalAmount;
            }
        }

        $fundTransfer = Fundtransferdetail::query();
        $fundTransferAmount = $fundTransfer->sum('trans_fee') ?? 0;
        $walletDetails['fund_transfer_fee'] = [
            'type' => 'income',
            'amount' => $fundTransferAmount,
        ];
        $walletTotal['income'] += $fundTransferAmount;

        $amountpaidDetails = AmountPaid::query();
        $payoutFeeAmount = $amountpaidDetails->sum('payout_fee') ?? 0;

        $walletDetails['payout_fee'] = [
            'type' => 'income',
            'amount' => $payoutFeeAmount,
        ];
        $walletTotal['income'] += $payoutFeeAmount;

        $enabledBonusList = $bonusList;

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

        // $legAmount = LegAmount::query();
        $serviceChargeAmount = TotalIncome::whereIn('amount_type', $enabledBonusList)->sum('service_charge') ?? 0;

        $walletDetails['commission_charge'] = [
            'type' => 'income',
            'amount' => $serviceChargeAmount,
        ];

        $walletTotal['income'] += $serviceChargeAmount;

        foreach ($enabledBonusList as $bonus) {
            $walletDetails[$bonus] = [
                'type' => 'bonus',
                'amount' => 0,
            ];
            if (in_array($bonus, array_keys($combinedTypes))) {
                $walletDetails[$combinedTypes[$bonus]] = $walletDetails[$bonus];
                unset($walletDetails[$bonus]);
            }
        }

        $legAmountDetails = TotalIncome::whereIn('amount_type', $enabledBonusList)
            ->select('amount_type', DB::raw('SUM(amount_payable) As amount'))
            ->groupBy('amount_type')->get();

        foreach ($legAmountDetails as $details) {
            if (in_array($details['amount_type'], array_keys($combinedTypes))) {
                $details['amount_type'] = $combinedTypes[$details['amount_type']];
            }
            $walletDetails[$details['amount_type']]['amount'] += $details['amount'];
        }

        $commission_amount = $legAmountDetails->sum('amount_payable') ?? 0;
        $walletTotal['bonus'] += $commission_amount;

        // $amountpaidDetails        =   AmountPaid::query();
        $payoutApprovedPaidAmount = $amountpaidDetails->where('type', 'released')->where('status', '1')->sum('amount') ?? 0;
        $walletTotal['paid'] += $payoutApprovedPaidAmount;

        $walletDetails['paid'] = [
            'type' => 'paid',
            'amount' => $payoutApprovedPaidAmount,
        ];

        $payoutApprovedPending = $amountpaidDetails->where('type', 'released')->where('status', '0')->where('payment_method', '=', 'bank')->sum('amount');
        $payoutRequestpendingDet = PayoutReleaseRequest::query();

        $payoutRequestsPending = $payoutRequestpendingDet->where('status', '0')->sum('balance_amount');

        $walletDetails['pending'] = [
            'type' => 'pending',
            'amount' => $payoutRequestsPending + $payoutApprovedPending,
        ];
        $walletTotal['pending'] += ($payoutRequestsPending + $payoutApprovedPending);

        return $walletDetails;
    }

    public function incomeAndCommission($moduleStatus)
    {
        $income = [];
        $commission = [];
        $incomeAndComm = [];
        $wallet_details = $this->getWalletDetails($moduleStatus);
        $i = 0;
        //top 4 income
        foreach ($wallet_details as $key => $value) {
            if ($value['type'] == 'income') {
                $income[$i]['type'] = $key;
                $income[$i]['amount'] = $value['amount'];
                $i++;
            }
        }
        usort($income, function ($a, $b) {
            if ($a['amount'] == $b['amount']) {
                return 0;
            }

            return $a['amount'] < $b['amount'] ? 1 : -1;
        });
        $income = array_slice($income, 0, 4);
        // Top 4 commission
        $i = 0;
        foreach ($wallet_details as $key => $value) {
            if ($value['type'] == 'bonus') {
                $commission[$i]['type'] = $key;
                $commission[$i]['amount'] = $value['amount'];
                $i++;
            }
        }
        usort($commission, function ($a, $b) {
            if ($a['amount'] == $b['amount']) {
                return 0;
            }

            return $a['amount'] < $b['amount'] ? 1 : -1;
        });
        $commission = array_slice($commission, 0, 4);
        $incomeAndComm['income'] = $income;
        $incomeAndComm['commission'] = $commission;

        return $incomeAndComm;
    }

    public function getTotalAmountApproved($userId = '')
    {
        // $amountPaid = AmountPaid::Active()->Released()->BankTransfer()->sum('amount');
        $amountPaid = AmountPaid::Pending()->Released()->BankTransfer()->sum('amount');

        return $amountPaid;
    }

    public function getTotalAmountPendingRequest($userId)
    {
        $amountPending = PayoutReleaseRequest::Pending()->sum('balance_amount');

        return $amountPending;
    }

    public function getIncomeBonusBarChartData($moduleStatus, $type = 'month')
    {
        $ewalletService = new EwalletService;
        $coreInf = new coreInf;
        $wallet_details = [];
        $labels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
        $incomeArray = [];
        $bonusArray = [];
        $regAmount = 0;
        $from_date = date('Y-01-01 00:00:00');
        if ($type == 'month') {
            if (date('m') == 12) {
                $newlabels = [];
                for ($i = 0; $i < 12; $i++) {
                    $plusVal = $i + 1;
                    $newlabels[$i] = [
                        date('Y') => strval($plusVal)
                    ];
                }
                $incomeArray[date('Y')] = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0];
                $bonusArray[date('Y')] = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0];
            } else {
                $labelsPre = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                $newlabels = [];
                $curMonth = date('m') * 1;
                $curMonthIndex = $curMonth - 1;

                for ($i = ($curMonthIndex + 1); $i < count($labelsPre); $i++) {
                    $newlabels[][(date('Y') - 1)] = $labelsPre[$i];
                }
                for ($i = 0; $i <= $curMonthIndex; $i++) {
                    $newlabels[][date('Y')] = $labelsPre[$i];
                }
                foreach ($newlabels as $key => $value) {
                    $incomeArray[array_keys($value)[0]][array_values($value)[0]] = 0;
                    $bonusArray[array_keys($value)[0]][array_values($value)[0]] = 0;
                }
                $nextMonthFirstDay = date('Y-' . ($curMonth + 1) . '-01');
                $from_date = date('Y-m-01', strtotime('-1 year', strtotime($nextMonthFirstDay)));
            }
        } elseif ($type == 'year') {
            $incomeArray = [];
            $newlabels = [];
            $bonusArray = [];
            $curYear = date('Y') * 1;
            $labels = [];
            $i = $curYear - 5; // 6 - 1
            while ($i <= $curYear) {
                $newlabels[] = $i;
                $i++;
            }
            foreach ($newlabels as $k => $va) {
                $incomeArray[$va] = 0;
                $bonusArray[$va] = 0;
            }
            $from_date = date('Y-01-01 00:00:00', strtotime('-5 year'));
        }
        if ($moduleStatus->ecom_status) {
            $ocOrder = OcOrder::query();
            if ($type == 'month') {
                $res = $ocOrder->selectRaw('SUM(total) as total,YEAR(date_added) as year, MONTH(date_added) as month')
                    ->whereDate('date_added', '>=', $from_date)->where('order_status_id', 5)->groupBy('year')->groupBy('month')->get();
                foreach ($res as $key => $item) {
                    $incomeArray[$item->year][$item->month] += ($moduleStatus->ecom_status) ? $item->total : 0;
                }
            } elseif ($type == 'year') {
                $res = $ocOrder->selectRaw('SUM(total) as total, YEAR(date_added) as year')
                    ->whereDate('date_added', '>=', $from_date)->groupBy('year')->get();
                foreach ($res as $key => $item) {
                    $incomeArray[$item->year] += ($moduleStatus->ecom_status) ? $item->total : 0;
                }
            }
        } else {
            $userRegisteration = UsersRegistration::query();
            if ($type == 'month') {
                $res = $userRegisteration->selectRaw('sum(reg_amount) as regAmount, sum(product_amount) as productAmount,DATE_FORMAT(ANY_VALUE(created_at), "%Y") AS year, DATE_FORMAT(created_at, "%c") AS month')
                    ->whereBetween('created_at', [$from_date, now()])->groupBy('year')->groupBy('month')->get();
                foreach ($res as $key => $item) {
                    $incomeArray[$item->year][$item->month] += ($moduleStatus->product_status) ? $item->productAmount : $item->regAmount;
                }
            } elseif ($type == 'year') {
                $res = $userRegisteration->selectRaw('sum(reg_amount) as regAmount, sum(product_amount) as productAmount,YEAR(ANY_VALUE(created_at)) as year')
                    ->whereBetween('created_at', [$from_date, now()])->groupBy('year')->get();
                foreach ($res as $key => $item) {
                    $incomeArray[$item->year] += ($moduleStatus->product_status) ? $item->productAmount : $item->regAmount;
                }
            }
            if ($moduleStatus['repurchase_status']) {
                $repurchaseOrder = Order::query();
                if ($type == 'month') {
                    $res = $repurchaseOrder->selectRaw('sum(total_amount) as totalAmount,DATE_FORMAT(created_at, "%Y") AS year, DATE_FORMAT(ANY_VALUE(created_at), "%c") AS month')->where('order_status', '1')->whereBetween('created_at', [$from_date, now()])->groupBy('year')->groupBy('month')->get();
                    foreach ($res as $key => $value) {
                        $incomeArray[$item->year][$item->month] += $value->totalAmount;
                    }
                } elseif ($type == 'year') {
                    $res = $repurchaseOrder->selectRaw('sum(total_amount) as totalAmount,YEAR(ANY_VALUE(created_at)) as year')->where('order_status', '1')
                        ->whereBetween('created_at', [$from_date, now()])->groupBy('year')->get();
                    foreach ($res as $key => $value) {
                        $incomeArray[$item->year] += $value->totalAmount;
                    }
                }
            }
            if ($moduleStatus['package_upgrade']) {
                $upgradeSalesOrder = UpgradesalesOrder::query();
                if ($type == 'month') {
                    $res = $upgradeSalesOrder->selectRaw('sum(amount) as amount, DATE_FORMAT(ANY_VALUE(created_at), "%Y") AS year, DATE_FORMAT(ANY_VALUE(created_at), "%c") AS month')->whereBetween('created_at', [$from_date, now()])->groupBy('year')->groupBy('month')->get();
                    foreach ($res as $key => $value) {
                        $incomeArray[$item->year][$item->month] += $value->amount;
                    }
                } elseif ($type == 'year') {
                    $res = $upgradeSalesOrder->selectRaw('sum(amount) as amount, YEAR(ANY_VALUE(created_at)) as year')->whereBetween('created_at', [$from_date, now()])->groupBy('year')->get();
                    foreach ($res as $key => $value) {
                        $incomeArray[$item->year] += $value->amount;
                    }
                }
            }
            if ($moduleStatus['subscription_status']) {
                $packageValidity = Packagevalidityextendhistory::query();
                if ($type == 'month') {
                    $res = $packageValidity->selectRaw('sum(total_amount) as amount, DATE_FORMAT(ANY_VALUE(created_at), "%Y") AS year, DATE_FORMAT(created_at, "%c") AS month')->whereBetween('created_at', [$from_date, now()])->groupBy('year')->groupBy('month')->get();
                    foreach ($res as $key => $value) {
                        $incomeArray[$item->year][$item->month] += $value->amount;
                    }
                } elseif ($type == 'year') {
                    $res = $packageValidity->selectRaw('sum(total_amount) as amount, YEAR(ANY_VALUE(created_at)) as year')->whereBetween('created_at', [$from_date, now()])->groupBy('year')->get();
                    foreach ($res as $key => $value) {
                        $incomeArray[$item->year] += $value->amount;
                    }
                }
            }
        }

        $amountPaid = AmountPaid::query();
        $legAmount = LegAmount::query();
        $fundTransferDetails = Fundtransferdetail::query();
        $enabledBonusList = $ewalletService->getEnabledBonuses($moduleStatus, $coreInf->compensation());
        if ($type == 'month') {
            $res = $fundTransferDetails->selectRaw('sum(trans_fee) as transactionFee,DATE_FORMAT(ANY_VALUE(created_at), "%Y") AS year, DATE_FORMAT(created_at, "%c") AS month')->where('amount_type', 'user_credit')->whereBetween('created_at', [$from_date, now()])->groupBy('year')->groupBy('month')->get();
            foreach ($res as $key => $value) {
                $incomeArray[$value->year][$value->month] += $value->transactionFee;
            }

            $res = $legAmount->selectRaw('sum(tds +service_charge) as total,sum(amount_payable) as amount_payable, DATE_FORMAT(ANY_VALUE(created_at), "%Y") AS year, DATE_FORMAT(created_at, "%c") AS month')->whereIn('amount_type', [...$enabledBonusList])->whereBetween('created_at', [$from_date, now()])->groupBy('year')->groupBy('month')->get();
            foreach ($res as $key => $value) {
                $incomeArray[$value->year][$value->month] += $value->total;
                $bonusArray[$value->year][$value->month] += $value->amount_payable;
            }

            $res = $amountPaid->selectRaw('sum(payout_fee) as payout_fee,YEAR(ANY_VALUE(created_at)) as year, MONTH(ANY_VALUE(created_at)) month')->where('payout_fee', '>', 0)->whereBetween('created_at', [$from_date, now()])->groupBy('year')->groupBy('month')->get();

            foreach ($res as $key => $value) {
                $incomeArray[$value->year][$value->month] += $value->payout_fee;
            }
        } elseif ($type == 'year') {
            $res = $fundTransferDetails->selectRaw('sum(trans_fee) as transactionFee,YEAR(ANY_VALUE(created_at)) as year')->where('amount_type', 'user_credit')->whereBetween('created_at', [$from_date, now()])->groupBy('year')->get();

            foreach ($res as $key => $value) {
                $incomeArray[$value->year] += $value->transactionFee;
            }
            $res = $legAmount->selectRaw('sum(tds +service_charge) as total,sum(amount_payable) as amount_payable, YEAR(ANY_VALUE(created_at)) as year')->whereIn('amount_type', [...$enabledBonusList])->whereBetween('created_at', [$from_date, now()])->groupBy('year')->get();

            foreach ($res as $key => $value) {
                $incomeArray[$value->year] += $value->total;
                $bonusArray[$value->year] += $value->amount_payable;
            }

            $res = $amountPaid->selectRaw('sum(payout_fee) as payout_fee,YEAR(ANY_VALUE(created_at)) as year')->where('payout_fee', '>', 0)->whereBetween('created_at', [$from_date, now()])->groupBy('year')->get();

            foreach ($res as $key => $value) {
                $incomeArray[$value->year] += $value->payout_fee;
            }
        }
        $resultIncome = [];
        $resultBonus = [];
        $graphLabel = [];
        if ($type == 'month') {
            foreach ($newlabels as $key1 => $label) {
                foreach ($incomeArray as $arryYear => $value) {
                    $year = array_keys($label)[0];
                    $month = array_values($label)[0];
                    if (isset($incomeArray[$year][$month])) {
                        $resultIncome[$key1] = $incomeArray[$year][$month];
                        $dateObj = DateTime::createFromFormat('!m', $month);
                        $graphLabel[$key1] = "$year " . $dateObj->format('M');
                    } else {
                        $resultIncome[$key1] = 0.0;
                    }
                }
                foreach ($bonusArray as $key => $bonusValue) {
                    $year = array_keys($label)[0];
                    $month = array_values($label)[0];
                    if (isset($bonusArray[$year][$month])) {
                        $resultBonus[$key1] = $bonusArray[$year][$month];
                    } else {
                        $resultBonus[$key1] = 0.0;
                    }
                }
            }
        } elseif ($type == 'year') {
            $graphLabel = $newlabels;
            foreach ($newlabels as $key1 => $label) {
                foreach ($incomeArray as $arryYear => $value) {
                    if ($label == $arryYear) {
                        $resultIncome[$key1] = $value;
                    }
                }
                foreach ($bonusArray as $yr => $bonusValue) {
                    if ($label == $yr) {
                        $resultBonus[$key1] = $bonusValue;
                    }
                }
            }
        }
        return compact('graphLabel', 'resultIncome', 'resultBonus');
    }

    public function getTopEarners()
    {
        $topEarners = User::has('Aggrigate')->select('username', 'id', 'user_type')->where('user_type', 'user')->with(['Aggrigate' => function ($query) {
            $query->select(DB::raw('sum(amount_payable) as total_amount_payable'), 'user_id')->where('amount_payable', '>', 0)->groupBy('user_id');
        }, 'userDetail:name,second_name,image,user_id,id'])->limit(4)->get()->sortByDesc('Aggrigate.total_amount_payable');

        return $topEarners;
    }

    public function getTopRecruiters()
    {
        $prefix = config('database.connections.mysql.prefix');
        $topRecruiters = DB::select("
        WITH SponsorDescendantCounts AS (
            SELECT
                COUNT(t.descendant) AS count,
                ANY_VALUE(t.descendant) AS descendant_id,
                ANY_VALUE(u.username) AS username,
                ANY_VALUE(u.id) AS id
            FROM
            {$prefix}treepaths AS t
            JOIN
            {$prefix}sponsor_treepaths AS st ON st.ancestor = t.descendant AND st.depth = 1
            JOIN
            {$prefix}users AS u ON u.id = st.ancestor
            WHERE
                t.ancestor = 1
            GROUP BY
                st.ancestor
        )
        SELECT
            count,
            descendant_id AS id,
            username,
            name,
            second_name AS secondName,
            gender,
            image
        FROM
            SponsorDescendantCounts
        JOIN {$prefix}user_details ON {$prefix}user_details.user_id = SponsorDescendantCounts.id
        ORDER BY
            count DESC
        LIMIT 5;
    ");
        return $topRecruiters;
    }

    public function getPackageProgressData()
    {
        $coreInf = new coreInf;

        $moduleStatus = $coreInf->moduleStatus();
        $data = [];

        if ($moduleStatus->ecom_status) {
            $groupBy = 'oc_product_id';
        } else {
            $groupBy = 'product_id';
        }

        if ($moduleStatus->ecom_status) {
            $packageOverView = OCProduct::withCount(['users' => fn ($qry) => $qry->where('user_type', 'user')])
                ->where('package_type', 'registration')
                ->where('status', 1)
                ->get();
            foreach ($packageOverView as $key1 => $value) {
                $data[$key1] = [
                    'name' => $value->model,
                    'count' => $value->users_count,
                ];
            }
        } else {
            $packageOverView = Package::withCount(['users' => fn ($qry) => $qry->where('user_type', 'user')])->ActiveRegPackage()->get();
            foreach ($packageOverView as $key1 => $value) {
                $data[$key1] = [
                    'name' => $value->name,
                    'count' => $value->users_count,
                ];
            }
        }

        return $data;
    }

    public function getRankData()
    {
        $coreInf = new coreInf;
        $moduleStatus = $coreInf->moduleStatus();
        $data = [];
        if ($moduleStatus['rank_status']) {
            $rankUsers = Rank::withCount(['users' => fn ($qry) => $qry->where('user_type', 'user')])->Active()->having('users_count', '>', 0)->get();
            foreach ($rankUsers as $key1 => $rank) {
                $data[$key1] = [
                    'name' => $rank->name ?? '',
                    'count' => $rank->users_count,
                ];
            }
        }

        return $data;
    }

    public function getJoiningGraphData($moduleStatus, $type = 'month')
    {
        $graphLabel = [];
        $from_date  = date("Y-01-01 00:00:00");

        if ($type == 'month') {
            if (date('m') == 12) {
                $newlabels = [];
                for ($i = 0; $i < 12; $i++) {
                    $newlabels[$i] = [
                        date('Y') => $i + 1
                    ];
                }
                $joinArray[date('Y')] = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0];
            } else {
                $labelsPre = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                $newlabels = [];
                $curMonth = date('m') * 1;
                $curMonthIndex = $curMonth - 1;
                for ($i = ($curMonthIndex + 1); $i < count($labelsPre); $i++) {
                    $newlabels[][(date('Y') - 1)] = $labelsPre[$i];
                }
                for ($i = 0; $i <= $curMonthIndex; $i++) {
                    $newlabels[][date('Y')] = $labelsPre[$i];
                }
                foreach ($newlabels as $key => $value) {
                    $joinArray[array_keys($value)[0]][array_values($value)[0]] = 0;
                }
                $nextMonthFirstDay = date('Y-' . ($curMonth + 1) . '-01');
                $from_date = date('Y-m-01', strtotime('-1 year', strtotime($nextMonthFirstDay)));
            }
        } elseif ($type == 'year') {
            $joinArray = [];
            $newlabels = [];
            $bonusArray = [];
            $curYear = date('Y') * 1;
            $labels = [];
            $i = $curYear - 5; // 6 - 1
            while ($i <= $curYear) {
                $newlabels[] = $i;
                $i++;
            }
            foreach ($newlabels as $k => $va) {
                $joinArray[$va] = 0;
            }
            $from_date = date('Y-01-01 00:00:00', strtotime('-5 year'));
        } elseif ($type == 'day') {
            $newlabels = [];
            $joinArray = [];
            $from_date = date('Y-m-d', strtotime('-11 days'));
            $dt = $from_date;
            $dt_end = date('Y-m-d');
            while ($dt <= $dt_end) {
                if (date('Y-m', strtotime($from_date)) == date('Y-m', strtotime($dt_end)) || date('Y', strtotime($from_date)) == date('Y', strtotime($dt_end))) {
                    $newlabels[] = date('M d', strtotime($dt));
                } else {
                    $newlabels[] = date('Y-m-d', strtotime($dt));
                }
                $dt = date('Y-m-d', strtotime('+1 day', strtotime($dt)));
            }
            foreach ($newlabels as $k => $va) {
                $joinArray[$va] = 0;
            }
        }
        if ($moduleStatus->mlm_plan == 'Binary') {
        }
        $joinQry = User::selectRaw('Year(date_of_joining) as year, MONTH(date_of_joining) as month, count(username) as count, username')
            ->where('id', '!=', auth()->user()->id)
            ->groupBy('username')
            ->groupBy('year')
            ->groupBy('month');

        if ($type == 'month') {
            $joinQry->whereDate('date_of_joining', '>=', $from_date);
        } elseif ($type == 'year') {
        } elseif ($type == 'day') {
        }
        $totalJoinings = $joinQry->get();
        if ($type == 'month') {
            foreach ($totalJoinings as $key => $value) {
                $joinArray[$value->year][$value->month] += $value->count;
            }
            foreach ($newlabels as $key => $label) {
                foreach ($joinArray as $key2 => $count) {
                    $year = array_keys($label)[0];
                    $month = array_values($label)[0];
                    if (isset($joinArray[$year][$month])) {
                        $joinArrayCount[$key] = $joinArray[$year][$month];
                    } else {
                        $joinArrayCount[$key] = 0;
                    }
                    $dateObj = DateTime::createFromFormat('!m', $month);
                    $graphLabel[$key] = "$year " . $dateObj->format('M');
                }
            }
        } elseif ($type == 'year') {
            foreach ($totalJoinings as $key => $value) {
                $joinArray[$value->year] += $value->count;
            }
            foreach ($newlabels as $key => $label) {
                foreach ($joinArray as $key2 => $count) {
                    if (isset($joinArray[$label])) {
                        $joinArrayCount[$key] = $joinArray[$label];
                    } else {
                        $joinArrayCount[$key] = 0;
                    }
                    $graphLabel[$key] = "$label";
                }
            }
        } elseif ($type == 'day') {
            foreach ($totalJoinings as $key => $value) {
                if (date('Y-m', strtotime($from_date)) == date('Y-m', strtotime($dt_end)) || date('Y', strtotime($from_date)) == date('Y', strtotime($dt_end))) {
                    $month = date('M', strtotime($value->year));
                    $day = date('d', strtotime($value->year));
                    $joinArray["$month $day"] += $value->count;
                } else {
                    $year = date('Y', strtotime($value->year));
                    $month = date('m', strtotime($value->year));
                    $day = date('d', strtotime($value->year));
                    $joinArray["$year-$month-$day"] += $value->count;
                }
            }

            foreach ($newlabels as $key => $label) {
                foreach ($joinArray as $key2 => $count) {
                    if (isset($joinArray[$label])) {
                        $joinArrayCount[$key] = $joinArray[$label];
                    } else {
                        $joinArrayCount[$key] = 0;
                    }
                    $graphLabel[$key] = "$label";
                }
            }
        }

        return compact('graphLabel', 'joinArrayCount');
    }


    public function getRightJoiningsGraph($type)
    {
        $graphLabel = [];
        $from_date  = date("Y-01-01 00:00:00");

        if ($type == 'month') {
            if (date('m') == 12) {
                $newlabels = [];
                for ($i = 0; $i < 12; $i++) {
                    $newlabels[$i] = [
                        date('Y') => $i + 1
                    ];
                }
                $joinArray[date('Y')] = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0];
            } else {
                $labelsPre = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                $newlabels = [];
                $curMonth = date('m') * 1;
                $curMonthIndex = $curMonth - 1;
                for ($i = ($curMonthIndex + 1); $i < count($labelsPre); $i++) {
                    $newlabels[][(date('Y') - 1)] = $labelsPre[$i];
                }
                for ($i = 0; $i <= $curMonthIndex; $i++) {
                    $newlabels[][date('Y')] = $labelsPre[$i];
                }
                foreach ($newlabels as $key => $value) {
                    $joinArray[array_keys($value)[0]][array_values($value)[0]] = 0;
                }
                $nextMonthFirstDay = date('Y-' . ($curMonth + 1) . '-01');
                $from_date = date('Y-m-01', strtotime('-1 year', strtotime($nextMonthFirstDay)));
            }
        } elseif ($type == 'year') {
            $joinArray = [];
            $newlabels = [];
            $bonusArray = [];
            $curYear = date('Y') * 1;
            $labels = [];
            $i = $curYear - 5; // 6 - 1
            while ($i <= $curYear) {
                $newlabels[] = $i;
                $i++;
            }
            foreach ($newlabels as $k => $va) {
                $joinArray[$va] = 0;
            }
            $from_date = date('Y-01-01 00:00:00', strtotime('-5 year'));
        } elseif ($type == 'day') {
            $newlabels = [];
            $joinArray = [];
            $from_date = date('Y-m-d', strtotime('-11 days'));
            $dt = $from_date;
            $dt_end = date('Y-m-d');
            while ($dt <= $dt_end) {
                if (date('Y-m', strtotime($from_date)) == date('Y-m', strtotime($dt_end)) || date('Y', strtotime($from_date)) == date('Y', strtotime($dt_end))) {
                    $newlabels[] = date('M d', strtotime($dt));
                } else {
                    $newlabels[] = date('Y-m-d', strtotime($dt));
                }
                $dt = date('Y-m-d', strtotime('+1 day', strtotime($dt)));
            }
            foreach ($newlabels as $k => $va) {
                $joinArray[$va] = 0;
            }
        }
        $rightJoin = $joinArray;
        $rightJoinCount = [];

        $rightNode = User::where('id', auth()->user()->id)->with(['children' => fn ($child) => $child->where('leg_position', 2)])->first();

        if (!$rightNode->children->isEmpty()) {
            $rightNodeUser = $rightNode->children[0];
            $rightJoinQry = User::query();

            $rightJoinQry->leftJoin('treepaths', 'treepaths.descendant', 'users.id')
                ->where('ancestor', $rightNodeUser->id)
                ->groupBy('username');

            if ($type == 'month') {
                if (date('m') == 12) {
                    $rightJoinQry->groupBy('year')
                        ->groupBy('month')
                        ->selectRaw('Year(date_of_joining) as year, MONTH(date_of_joining) as month, count(username) as count, username')
                        ->whereDate('date_of_joining', '>=', $from_date);
                } else {
                    $rightJoinQry->groupBy('year')
                        ->groupBy('month')
                        ->selectRaw('Year(date_of_joining) as year, MONTH(date_of_joining) as month, count(username) as count, username')
                        ->whereDate('date_of_joining', '>=', $from_date);
                }
            } elseif ($type == 'year') {
                $rightJoinQry->groupBy('year')
                    ->selectRaw('Year(date_of_joining) as year, count(username) as count, username')
                    ->whereDate('date_of_joining', '>=', $from_date);
            } elseif ($type == 'day') {
                $rightJoinQry->selectRaw('Year(date_of_joining) as year,Month(date_of_joining) as month,Day(date_of_joining) as day, count(username) as count, username')
                    ->whereDate('date_of_joining', '>=', $from_date);
            }
            $rightCount = $rightJoinQry->get();
            if ($type == 'month') {
                foreach ($rightCount as $key => $value) {
                    $rightJoin[$value->year][$value->month] += $value->count;
                }
                foreach ($newlabels as $key => $label) {
                    foreach ($rightJoin as $key2 => $count) {
                        $year = array_keys($label)[0];
                        $month = array_values($label)[0];
                        if (isset($rightJoin[$year][$month])) {
                            $rightJoinCount[$key] = $rightJoin[$year][$month];
                        } else {
                            $rightJoinCount[$key] = 0;
                        }
                        $dateObj = DateTime::createFromFormat('!m', $month);
                        $graphLabel[$key] = "$year " . $dateObj->format('M');
                    }
                }
                // dd($rightJoinCount);
            } elseif ($type == 'year') {
                foreach ($rightCount as $key => $value) {
                    $rightJoin[$value->year] += $value->count;
                }
                foreach ($newlabels as $key => $label) {
                    foreach ($rightJoin as $key2 => $count) {
                        if (isset($rightJoin[$label])) {
                            $rightJoinCount[$key] = $rightJoin[$label];
                        } else {
                            $rightJoinCount[$key] = 0;
                        }
                        $graphLabel[$key] = "$label";
                    }
                }
            } elseif ($type == 'day') {
                foreach ($rightCount as $key => $value) {
                    if (date('Y-m', strtotime($from_date)) == date('Y-m', strtotime($dt_end)) || date('Y', strtotime($from_date)) == date('Y', strtotime($dt_end))) {
                        $month = date('M', strtotime($value->year));
                        $day = date('d', strtotime($value->year));
                        $rightJoin["$month $day"] += $value->count;
                    } else {
                        $year = date('Y', strtotime($value->year));
                        $month = date('m', strtotime($value->year));
                        $day = date('d', strtotime($value->year));
                        $rightJoin["$year-$month-$day"] += $value->count;
                    }
                }

                foreach ($newlabels as $key => $label) {
                    foreach ($rightJoin as $key2 => $count) {
                        if (isset($rightJoin[$label])) {
                            $rightJoinCount[$key] = $rightJoin[$label];
                        } else {
                            $rightJoinCount[$key] = 0;
                        }
                        $graphLabel[$key] = "$label";
                    }
                }
            }
        }
        return compact('graphLabel', 'rightJoinCount');
    }

    public function getLeftJoiningsGraph($type)
    {
        $graphLabel = [];
        $from_date = date("Y-01-01 00:00:00");
        if ($type == 'month') {
            if (date('m') == 12) {
                $newlabels = [];
                for ($i = 0; $i < 12; $i++) {
                    $newlabels[$i] = [
                        date('Y') => $i + 1
                    ];
                }
                $joinArray[date('Y')] = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0];
            } else {
                $labelsPre = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                $newlabels = [];
                $curMonth = date('m') * 1;
                $curMonthIndex = $curMonth - 1;
                for ($i = ($curMonthIndex + 1); $i < count($labelsPre); $i++) {
                    $newlabels[][(date('Y') - 1)] = $labelsPre[$i];
                }
                for ($i = 0; $i <= $curMonthIndex; $i++) {
                    $newlabels[][date('Y')] = $labelsPre[$i];
                }
                foreach ($newlabels as $key => $value) {
                    $joinArray[array_keys($value)[0]][array_values($value)[0]] = 0;
                }
                $nextMonthFirstDay = date('Y-' . ($curMonth + 1) . '-01');
                $from_date = date('Y-m-01', strtotime('-1 year', strtotime($nextMonthFirstDay)));
            }
        } elseif ($type == 'year') {
            $joinArray = [];
            $newlabels = [];
            $bonusArray = [];
            $curYear = date('Y') * 1;
            $labels = [];
            $i = $curYear - 5; // 6 - 1
            while ($i <= $curYear) {
                $newlabels[] = $i;
                $i++;
            }
            foreach ($newlabels as $k => $va) {
                $joinArray[$va] = 0;
            }
            $from_date = date('Y-01-01 00:00:00', strtotime('-5 year'));
        } elseif ($type == 'day') {
            $newlabels = [];
            $joinArray = [];
            $from_date = date('Y-m-d', strtotime('-11 days'));
            $dt = $from_date;
            $dt_end = date('Y-m-d');
            while ($dt <= $dt_end) {
                if (date('Y-m', strtotime($from_date)) == date('Y-m', strtotime($dt_end)) || date('Y', strtotime($from_date)) == date('Y', strtotime($dt_end))) {
                    $newlabels[] = date('M d', strtotime($dt));
                } else {
                    $newlabels[] = date('Y-m-d', strtotime($dt));
                }
                $dt = date('Y-m-d', strtotime('+1 day', strtotime($dt)));
            }
            foreach ($newlabels as $k => $va) {
                $joinArray[$va] = 0;
            }
        }

        $leftJoin = $joinArray;
        $leftJoinCount = [];

        $leftNode = User::where('id', auth()->user()->id)->with(['children' => fn ($child) => $child->where('leg_position', 1)])->first();

        if (!$leftNode->children->isEmpty()) {
            $leftNodeUser = $leftNode->children[0];
            $joinQry = User::query();
            $joinQry->leftJoin('treepaths', 'treepaths.descendant', 'users.id')
                ->where('ancestor', $leftNodeUser->id)
                ->groupBy('username');
            if ($type == 'month') {
                $joinQry->groupBy('year')
                    ->groupBy('month')
                    ->selectRaw('Year(date_of_joining) as year, MONTH(date_of_joining) as month, count(username) as count, username')
                    ->whereDate('date_of_joining', '>=', $from_date);
            } elseif ($type == 'year') {
                $joinQry->groupBy('year')
                    ->selectRaw('Year(date_of_joining) as year, count(username) as count, username')
                    ->whereDate('date_of_joining', '>=', $from_date);
            } elseif ($type == 'day') {
                $joinQry->selectRaw('Year(date_of_joining) as year,Month(date_of_joining) as month,Day(date_of_joining) as day, count(username) as count, username')
                    ->whereDate('date_of_joining', '>=', $from_date);
            }
            $leftCount = $joinQry->get();

            if ($type == 'month') {
                foreach ($leftCount as $key => $value) {
                    $leftJoin[$value->year][$value->month] += $value->count;
                }
                foreach ($newlabels as $key => $label) {
                    foreach ($leftJoin as $key2 => $count) {
                        $year = array_keys($label)[0];
                        $month = array_values($label)[0];
                        if (isset($leftJoin[$year][$month])) {
                            $leftJoinCount[$key] = $leftJoin[$year][$month];
                        } else {
                            $leftJoinCount[$key] = 0;
                        }
                        $dateObj = DateTime::createFromFormat('!m', $month);
                        $graphLabel[$key] = "$year " . $dateObj->format('M');
                    }
                }
            } elseif ($type == 'year') {
                foreach ($leftCount as $key => $value) {
                    $leftJoin[$value->year] += $value->count;
                }
                foreach ($newlabels as $key => $label) {
                    foreach ($leftJoin as $key2 => $count) {
                        if (isset($leftJoin[$label])) {
                            $leftJoinCount[$key] = $leftJoin[$label];
                        } else {
                            $leftJoinCount[$key] = 0;
                        }
                        $graphLabel[$key] = "$label";
                    }
                }
            } elseif ($type == 'day') {
                foreach ($leftCount as $key => $value) {
                    if (date('Y-m', strtotime($from_date)) == date('Y-m', strtotime($dt_end)) || date('Y', strtotime($from_date)) == date('Y', strtotime($dt_end))) {
                        $month = date('M', strtotime($value->year));
                        $day = date('d', strtotime($value->year));
                        $leftJoin["$month $day"] += $value->count;
                    } else {
                        $year = date('Y', strtotime($value->year));
                        $month = date('m', strtotime($value->year));
                        $day = date('d', strtotime($value->year));
                        $leftJoin["$year-$month-$day"] += $value->count;
                    }
                }

                foreach ($newlabels as $key => $label) {
                    foreach ($leftJoin as $key2 => $count) {
                        if (isset($leftJoin[$label])) {
                            $leftJoinCount[$key] = $leftJoin[$label];
                        } else {
                            $leftJoinCount[$key] = 0;
                        }
                        $graphLabel[$key] = "$label";
                    }
                }
            }
        }
        return compact('graphLabel', 'leftJoinCount');
    }

    public function getTransactions(){
        $query  = collect([]);
        $data = EwalletPurchaseHistory::query();
        $data->select(['ewallet_purchase_histories.id','user_id','amount','amount_type','type','ewallet_type','ewallet_purchase_histories.created_at as createdAt', 'ewallet_purchase_histories.updated_at as updatedAt']);
        $query->push($data);
        $data2 = EwalletTransferHistory::query();
        $data2->select(['ewallet_transfer_histories.id', 'user_id','amount','amount_type','type',DB::raw("Null as ewallet_type"),'ewallet_transfer_histories.created_at as createdAt', 'ewallet_transfer_histories.updated_at as updatedAt']);
        $query->push($data2);
        $finalQuery = $query->shift();
        foreach ($query as $q) {
            $finalQuery->unionAll($q);
        }
        $finalQuery->orderBy('createdAt', 'desc');
        $finalQuery->take(5);
        $results = $finalQuery->get();
        return $results;
    }
}
