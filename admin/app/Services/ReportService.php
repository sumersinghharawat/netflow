<?php

namespace App\Services;

use App\Models\AmountPaid;
use App\Models\Configuration;
use App\Models\EpinTransferHistory;
use App\Models\LegAmount;
use App\Models\PackageUpgradeHistory;
use App\Models\PayoutConfiguration;
use App\Models\PayoutReleaseRequest;
use App\Models\Rank;
use App\Models\RankHistory;
use App\Models\RankUser;
use App\Models\User;
use App\Models\UserBalanceAmount;
use App\Models\Order;
use App\Models\Packagevalidityextendhistory;
use App\Models\UsersRegistration;
use App\Services\EwalletService;
use App\Http\Controllers\CoreInfController as coreInf;
use App\Models\OcOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;


class ReportService
{
    public function activateReport($request)
    {
        $data = User::query();
        $data = $data->where('user_type','user');

        if ($request->has('filter_type') && ($request->filter_type != 'overall')) {
            $toDate = date('Y-m-d 23:59:59', strtotime(now()->endOfDay()));

            if ($request->filter_type == 'today') {
                $fromDate = date('Y-m-d 00:00:00', strtotime(now()->startOfDay()));
            }

            if ($request->filter_type == 'month') {
                $fromDate = date('Y-m-d 00:00:00', strtotime(now()->startOfMonth()));
            }
            if ($request->filter_type == 'year') {
                $fromDate = date('Y-m-d 00:00:00', strtotime(now()->startOfYear()));
            }
            if ($request->filter_type == 'custom') {
                $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
                $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
            }
            $data = $data->whereBetween('date_of_joining', [$fromDate, $toDate]);
        }
        $users = $data->where('delete_status', '1')->with('userDetails:name,second_name,user_id,id', 'salesOrder:invoice_no,user_id,id')->get();

        return $users;
    }

    public function joingReport($request)
    {
        $joinDetails = User::with('sponsor', 'userDetails', 'package', 'userRegDetails')->paginate(10);
        $flag = 0;
        if ($request->daterange == 'today') {
            $today = date('Y-m-d');
            $joinDetails = User::where('date_of_joining', 'Like', '%' . $today . '%')->with('sponsor', 'userDetails', 'package', 'userRegDetails')->paginate(10);
            $flag = 1;
        }
        if ($request->daterange == 'month') {
            $month = date('m');
            $firstdayMonth = date("Y-$month-01");
            $lastdayMonth = date("Y-$month-t");
            $joinDetails = User::whereBetween('date_of_joining', [$firstdayMonth, $lastdayMonth])->with('sponsor', 'userDetails', 'package', 'userRegDetails')->paginate(10);
            $flag = 2;
        }
        if ($request->daterange == 'year') {
            User::whereYear('date_of_joining', date('Y'))->with('sponsor', 'userDetails', 'package', 'userRegDetails')->paginate(10);
            $flag = 3;
        }

        return $joinDetails;
    }

    public function rankAchieversReport($request)
    {
        // dd($request);
        // $data = RankHistory::query()->with('currentRank', 'newRank', 'User');
        $ranks = Rank::select('name', 'id')->get();
        $data = RankUser::query()->with('user', 'user.userDetails', 'rank');
        if ($request->has('username') && $request->username != '') {
            $data = $data->where('user_id', $request->username);
        }
        if ($request->has('rank') && $request->rank != '') {
            $data = $data->whereIn('rank_id', $request->rank);
        }

        if ($request->has('filter_type') && ($request->filter_type != 'overall')) {
            $toDate = date('Y-m-d 23:59:59', strtotime(now()->endOfDay()));

            if ($request->filter_type == 'today') {
                $fromDate = date('Y-m-d 00:00:00', strtotime(now()->startOfDay()));
            }

            if ($request->filter_type == 'month') {
                $fromDate = date('Y-m-d 00:00:00', strtotime(now()->startOfMonth()));
            }
            if ($request->filter_type == 'year') {
                $fromDate = date('Y-m-d 00:00:00', strtotime(now()->startOfYear()));
            }
            if ($request->filter_type == 'custom') {
                $fromDate = date('Y-m-d 00:00:00', strtotime($request->fromDate));
                $toDate = date('Y-m-d 23:59:59', strtotime($request->toDate));
            }

            $data->whereBetween('updated_at', [$fromDate, $toDate]);
        }
        $rankAchievers = $data->get();

        return $rankAchievers;
    }

    public function rankAchieversReportOld($request)
    {
        $data = RankHistory::query()->with('currentRank', 'newRank', 'User');
        if ($request->has('username') && $request->username != '') {
            $data = $data->where('user_id', $request->username);
        }
        if ($request->has('rank') && $request->rank != '') {
            $data = $data->whereIn('new_rank_id', $request->rank);
        }
        $rankAchievers = $data->get();

        return $rankAchievers;
    }

    public function upgradePackage($request)
    {
        $data = PackageUpgradeHistory::query()->with('currentPackage', 'upgradePackage', 'user.userDetail');
        if ($request->has('username') && $request->username != '') {
            $data = $data->where('user_id', $request->username);
        }
        if ($request->has('package') && $request->package != '') {
            $data = $data->where('current_package_id', $request->package);
        }

        return $data;
    }

    public function joinReport($request)
    {
        $data = UsersRegistration::query();   
        if ($request->daterange == 'today') {
            $data->whereHas('user', function ($query) {
                $query->where('date_of_joining', 'Like', '%' . date('Y-m-d') . '%');
            });
        }
        if ($request->daterange == 'month') {
            $month = date('m');
            $firstdayMonth = date("Y-$month-01");
            $lastdayMonth = date("Y-$month-t");
            $data->whereHas('user', function ($query) use($firstdayMonth, $lastdayMonth) {
                $query->whereBetween('date_of_joining', [$firstdayMonth, $lastdayMonth]);
            });
        }
        if ($request->daterange == 'year') {
            $data->whereHas('user', function ($query){
                $query->whereYear('date_of_joining', date('Y'));
            });
        }
        $joinDetails = $data->with('user.sponsor' , 'userDetail' , 'package')->whereHas('user', function ($query) {
            $query->where('user_type', 'user');
        })->get();
        return $joinDetails;
    }

    public function profileDateReport($request)
    {
        $user = User::query();
        if ($request->has('username') && $request->username != '') {
            $profileDetails = $user->whereKey($request->username);

            $result = $profileDetails->where('user_type', '!=', 'employee')->with('userDetails', 'userDetails.country')->get();

            return $result;
        }
        if ($request->has('fromDate') && $request->fromDate != '' && $request->has('toDate') && $request->toDate != '') {
            $profileDetails = $user->whereBetween('date_of_joining', [$request->fromDate . ' 00:00:00', $request->toDate . ' 23:59:59']);

            $result = $profileDetails->where('user_type', '!=', 'employee')->with('userDetails', 'userDetails.country')->get();

            return $result;
        }
    }

    public function commissionReport($request)
    {
        $coreInf        = new coreInf;
        $module_status  = $coreInf->moduleStatus();
        $legAmount      = LegAmount::query();

        $month          = now()->month;
        $year           = now()->year;
        $day            = now()->day;
        if ($request->has('username') && $request->username != '') {
            $legAmount->where('user_id', $request->username);
        }
        if ($request->has('filter_type') && ($request->filter_type != 'overall')) {
            switch ($request->filter_type) {
                case 'today':
                    $legAmount->whereDay('created_at', $day)->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'month':
                    $legAmount->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'year':
                    $legAmount->whereYear('created_at', $year);
                    break;
                default:
                    break;
            }
        }

        if ($request->has('commissionType')) {
            $legAmount->whereIn('amount_type', [...$request->commissionType]);
        }

        if ($request->filter_type == 'custom' && $request->fromDate != null && $request->toDate != null) {
            $legAmount->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
        }
        $commission = $legAmount->with('user.userDetails')->get();
        return $commission;
    }

    public function totalBonusReport($request)
    {
        // DB::enableQueryLog();
        $totalBonus = User::Select('username', 'id')->has('legamtDetails')->where('user_type', '!=', 'employee')->with('userDetails:id,name,second_name,user_id', 'legamtDetails')->withSum('legamtDetails as tds', 'tds')->withSum('legamtDetails as total_amount', 'total_amount')->withSum('legamtDetails as amount_payable', 'amount_payable')->withSum('legamtDetails as service_charge', 'service_charge')
            ->whereRelation('legamtDetails', [
                [
                    'total_amount', '>', 0,
                ],
                [
                    'amount_payable', '>', 0,
                ],
                [
                    'tds', '>', 0,
                ],
            ]);

        if ($request->has('username') && $request->username != null) {
            $totalBonus->whereKey($request->username);
        }

        $currency = currencySymbol();

        $month = now()->month;
        $year = now()->year;
        $day = now()->day;
        $today = 'today';
        switch ($request->filter_type) {
            case 'today':
                $totalBonus->whereRelation('legamtDetails', function (Builder $query) {
                    $month = now()->month;
                    $year = now()->year;
                    $day = now()->day;
                    $query->whereDay('created_at', $day)->whereMonth('created_at', $month)->whereYear('created_at', $year);
                });
                break;
            case 'month':
                $totalBonus->whereRelation('legamtDetails', function (Builder $query) {
                    $month = now()->month;
                    $year = now()->year;
                    $day = now()->day;
                    $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
                });
                break;
            case 'year':
                $totalBonus->whereRelation('legamtDetails', function (Builder $query) {
                    $month = now()->month;
                    $year = now()->year;
                    $day = now()->day;
                    $query->whereYear('created_at', $year);
                });
                break;
        }

        if ($request->filter_type == 'custom' && $request->fromDate != null && $request->toDate != null) {
            $totalBonus->whereRelation('legamtDetails', function (Builder $query) use ($request) {
                $query->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
            });
        }

        $configuration = $this->configuration();
        $showTDS = true;
        $showServiceCharge = true;
        $showAmountPayable = true;
        if (($configuration->tds <= 0) && (LegAmount::sum('tds') <= 0)) {
            $showTDS = false;
        }
        if (($configuration->service_charge <= 0) && (LegAmount::sum('service_charge') <= 0)) {
            $showServiceCharge = false;
        }
        if (!$showServiceCharge && !$showTDS) {
            $showAmountPayable = false;
        }
        
        return $totalBonus->get();
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

    public function payoutReport($request)
    {
        if ($request->has('status')) {
            if ($request->status == 'pending') {
                $configuration = PayoutConfiguration::first();
                $payoutSettings = [];
                $payoutSettings = [
                    'payoutType' => $configuration->release_type,
                    'minPayout' => $configuration->min_payout,
                    'maxPayout' => $configuration->max_payout,
                ];
                if ($payoutSettings['payoutType'] == 'ewallet_request' || $payoutSettings['payoutType'] == 'both') {
                    $data = PayoutReleaseRequest::query()->with('user')->where('status', '0');
                    $amountPaids = $data->get();
                    $payoutTotal = $data->get()->sum('amount');

                    $data1 = [];
                    $data1['amountPaids'] = $amountPaids;
                    $data1['payoutTotal'] = $payoutTotal;
                } elseif ($payoutSettings['payoutType'] == 'from_ewallet' || $payoutSettings['payoutType'] == 'both') {
                    $data = UserBalanceAmount::query()->with('user');
                    $amountPaids = $data->get();
                    $payoutTotal = UserBalanceAmount::sum('balance_amount');
                    $data1 = [];
                    $data1['amountPaids'] = $amountPaids;
                    $data1['payoutTotal'] = $payoutTotal;

                    return $data1;
                }
            }
            // elseif($request->status == "released")
            // {
            //     $data           =    AmountPaid::query()->with('user')->where('status',"1");
            //     $amountPaids    =    $data->get();
            //     $payoutTotal    =     AmountPaid::sum('amount');
            // }
        }

        // $data           =    AmountPaid::query()->with('user')->where('status',"1");
        // $amountPaids    =    $data->get();
        // $payoutTotal    =    $data->get()->sum('amount');
    }

    public function getPurchaseReports($request, $ecom_status = false)
    {
        try {
            if($ecom_status) {
                $order = OcOrder::query();
            } else {
                $order = Order::query();
            }

            if ($request->has('username') && $request->username != null) {
                $order->whereRelation('user', 'id', $request->username);
            }

            $month = now()->month;
            $year  = now()->year;
            $day   = now()->day;

            if ($request->has('filter_type') && ($request->filter_type != 'overall')) {
                switch ($request->filter_type) {
                    case 'today':
                        $order->whereDay('created_at', $day)->whereMonth('created_at', $month)->whereYear('created_at', $year);
                        break;
                    case 'month':
                        $order->whereMonth('created_at', $month)->whereYear('created_at', $year);
                        break;
                    case 'year':
                        $order->whereYear('created_at', $year);
                        break;
                    default:
                        break;
                }
            }


            if ($request->filter_type == 'custom' && $request->fromDate != null && $request->toDate != null) {
                if($ecom_status) {
                    $order->where('order_type', '!=' ,'register');
                    $order->whereDate('date_added', '>=', $request->fromDate)->whereDate('date_added', '<=', $request->toDate);
                } else {
                    $order->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
                }
            }
            if($ecom_status) {
                $order->where('order_type', '!=' ,'register');
                $order->orderBy('date_added', 'DESC')->where('order_status_id', '5')->with('customer', 'user.userDetail');
            } else {
                $order->latest()->where('order_status', '1')->with('paymentMethod:name,slug,id', 'user:username,id', 'user.userDetail:name,second_name,user_id');
            }
            $orders = $order;

            return $orders;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getSubscriptionReport($request)
    {
        try {
            $report = Packagevalidityextendhistory::query();

            if ($request->has('username') && $request->username != null) {
                $report->whereRelation('user', 'id', $request->username);
            }

            $reports = $report->with('user:username,id', 'user.userDetail:name,second_name,user_id', 'package', 'paymentMethod');

            return $reports;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getPayoutPendingReports($request)
    {
        try {
            $data = PayoutReleaseRequest::query();
            $month = now()->month;
            $year = now()->year;
            $day = now()->day;
            switch ($request->filter_type) {
                case 'today':
                    $data->whereDay('created_at', $day)->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'month':
                    $data->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'year':
                    $data->whereYear('created_at', $year);
                    break;
            }

            if ($request->filter_type == 'custom' && $request->fromDate != null && $request->toDate != null) {
                $data->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
            }

            $reports =  $data->with('user.userDetails:name,second_name,user_id,id')->where('status', '0');


            return $reports;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getUserAmountPendingReport($request, $configuration)
    {
        try {
            $data = UserBalanceAmount::query();
            $month = now()->month;
            $year = now()->year;
            $day = now()->day;
            switch ($request->filter_type) {
                case 'today':
                    $data->whereDay('created_at', $day)->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'month':
                    $data->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'year':
                    $data->whereYear('created_at', $year);
                    break;
            }

            if ($request->filter_type == 'custom' && $request->fromDate != null && $request->toDate != null) {
                $data->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
            }
            $total = $configuration->min_payout + $configuration->fee_amount;
            $amount = $this->calculateTotalPayoutAmount($total, $configuration);
            $reports = $data->with('user.userDetail:name,second_name,user_id,id')->where('balance_amount', '>=', $amount)->whereRelation('user', 'active', true)->whereRelation('user', 'user_type', 'user');
            return $reports;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function calculateTotalPayoutAmount($amount, $configuration)
    {
        try {
            $fee = $amount;
            if ($configuration->fee_mode == 'percentage') {
                $fee = $amount * $configuration->fee_amount / 100;
            }
            return $fee;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getReleasePayouts($request)
    {
        try {
            $data = AmountPaid::query();

            $month = now()->month;
            $year = now()->year;
            $day = now()->day;
            switch ($request->filter_type) {
                case 'today':
                    $data->whereDay('created_at', $day)->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'month':
                    $data->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'year':
                    $data->whereYear('created_at', $year);
                    break;
            }

            if ($request->filter_type == 'custom' && $request->fromDate != null && $request->toDate != null) {
                $data->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
            }

            $reports = $data->with('user.userDetail:name,second_name,user_id,id')->where('status', 1);

            return $reports;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getEpinTransferReports($request)
    {
        try {
            $data = EpinTransferHistory::query();
            $fromUser = null;
            $toUser = null;

            if ($request->has('fromUser') && $request->fromUser != null) {
                $data->where('from_user', $request->fromUser);
                $fromUser = User::select('username', 'id')->whereKey($request->fromUser)->first();
            }
            if ($request->has('toUser') && $request->toUser != null) {
                $data->where('to_user', $request->toUser);
                $toUser = User::select('username', 'id')->whereKey($request->toUser)->first();
            }

            $month = now()->month;
            $year = now()->year;
            $day = now()->day;
            switch ($request->filter_type) {
                case 'today':
                    $data->whereDay('created_at', $day)->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'month':
                    $data->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    break;
                case 'year':
                    $data->whereYear('created_at', $year);
                    break;
            }

            if ($request->filter_type == 'custom' && $request->fromDate != null && $request->toDate != null) {
                $data->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
            }

            $reports = $data->with('fromUser.userDetail', 'toUser.userDetail', 'epin');

            return $reports;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getTopEarnersReport()
    {
        try {
            $reports = User::select("username", 'id')->has('legamtDetails')->where('user_type', '!=', 'employee')->with('userDetails:id,name,second_name,user_id', 'userBalance:id,balance_amount,user_id')->with(['legamtDetails' => function ($query) {
                $query->select(DB::raw('sum(total_amount) as total_amount'), 'user_id')->where('total_amount', '>', 0)->groupBy('user_id');
            }]);

            return $reports;
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
