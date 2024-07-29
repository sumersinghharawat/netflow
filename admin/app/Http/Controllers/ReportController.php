<?php

namespace App\Http\Controllers;

use App\Http\Requests\PackageupgradeRequest;
use App\Http\Requests\RankAchieversRequest;
use App\Http\Requests\RequestActdeactivateReport;
use App\Models\AmountPaid;
use App\Models\EpinTransferHistory;
use App\Models\LegAmount;
use App\Models\OcOrder;
use App\Models\Package;
use App\Models\OCProduct;
use App\Models\Order;
use App\Models\PackageUpgradeHistory;
use App\Models\PayoutConfiguration;
use App\Models\Rank;
use App\Models\RankConfiguration;
use App\Models\RankUser;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\UsersRegistration;
use App\Services\EwalletService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends CoreInfController
{
    public function joining(Request $request)
    {
        $joinDetails = UsersRegistration::with('user.sponsor' , 'userDetail' , 'package')->whereHas('user', function ($query) {
            $query->where('user_type', 'user');
        });
        $flag = 0;
        if ($request->daterange == 'today') {
            $flag = 1;
            $joinDetails->whereHas('user', function ($query) {
                $query->where('date_of_joining', 'Like', '%' . date('Y-m-d') . '%');
            });
        }
        if ($request->daterange == 'month') {
            $flag = 2;
            $month = date('m');
            $firstdayMonth = date("Y-$month-01");
            $lastdayMonth = date("Y-$month-t");
            $joinDetails->whereHas('user', function ($query) use($firstdayMonth, $lastdayMonth) {
                $query->whereBetween('date_of_joining', [$firstdayMonth, $lastdayMonth]);
            });
        }
        if ($request->daterange == 'year') {
            $flag = 3;
            $joinDetails->whereHas('user', function ($query){
                $query->whereYear('date_of_joining', date('Y'));
            });
        }
        $regFeeStatus = $this->checkRegistrationFeeDisplay();

        $currency = currencySymbol();

        $joinDetails = $joinDetails->paginate(10)->withQueryString();
        $moduleStatus = $this->moduleStatus();

        return view('admin.reports.joining', compact('joinDetails', 'currency', 'regFeeStatus', 'flag', 'moduleStatus'));
    }

    public function epinTransfer(Request $request)
    {
        // dd($request->all());
        $data = EpinTransferHistory::query();
        $fromUser = null;
        $toUser = null;
        // if ($request->has('fromUser') && $request->fromUser != null) {
        //     $data->where('from_user', $request->fromUser);
        // }
        if ($request->has('fromUser') && $request->fromUser != null) {
            $data->where('from_user', $request->fromUser);
            $fromUser = User::select('username', 'id')->whereKey($request->fromUser)->first();
        }
        if ($request->has('toUser') && $request->toUser != null) {
            $data->where('to_user', $request->toUser);
            $toUser = User::select('username', 'id')->whereKey($request->toUser)->first();
        }
        if ($request->has('fromDate') && $request->has('toDate') && $request->date == 'custom') {
            $fromDate = $request->fromDate . ' ' . '00:00:00';
            $toDate = $request->toDate . ' ' . '23:59:59';
            $data->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $month = now()->month;
        $year = now()->year;
        $day = now();
        switch ($request->date) {
            case 'today':
                $data->whereDate('date', now()->format('Y-m-d H:i:s'));
                break;
            case 'year':
                $data->whereYear('date', $year);
                break;
            case 'month':
                $data->whereMonth('date', $month)->whereYear('date', $year);
            default:
                break;
        }
        $data->with('fromUser.userDetail', 'toUser.userDetail', 'epin');
        $epinTransferHistory = $data->paginate(10)->withQueryString();
        return view('admin.report.epinTransfer', compact('epinTransferHistory', 'fromUser', 'toUser'));
    }

    public function actDeactReport(RequestActdeactivateReport $request)
    {
        $moduleStatus   = $this->moduleStatus();
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
        if($moduleStatus->ecom_status)
        {
        $data->where('delete_status', '1')
        ->with('userDetails:name,second_name,user_id,id')
        ->with('ocOrder:invoice_prefix,customer_id,order_id')
        ->get();
        }
        else
        {
        $data->where('delete_status', '1')->with('userDetails:name,second_name,user_id,id', 'salesOrder:invoice_no,user_id,id')->get();
        }
        $users = $data->paginate(10)->withQueryString();
        return view('admin.report.activate-deactivateReport', compact('users' ,'moduleStatus'));
    }

    public function commissionReport(Request $request)
    {
        $request->validate([
            'fromDate' => 'required_if:filter_type,custom',
            'toDate' => 'required_if:filter_type,custom',
        ]);
        $module_status = $this->moduleStatus();
        $ewalletService = new EwalletService;
        $legAmount = LegAmount::query();
        $username = null;
        if ($request->has('username') && $request->username != '') {
            $legAmount->where('user_id', $request->username);
            $username = User::select('username', 'id')->whereKey($request->username)->first();
        }
        $month = now()->month;
        $year = now()->year;
        $day = now()->day;

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

        $month = now()->month;
        $year = now()->year;
        $day = now()->day;

        $showTDS = 'yes';
        $showServiceCharge = 'yes';
        $showAmountPayable = 'yes';
        $configuration = $this->configuration();

        if (($configuration->tds <= 0) && (LegAmount::sum('tds') <= 0)) {
            $showTDS = 'no';
        }

        if (($configuration->service_charge <= 0) && (LegAmount::sum('service_charge') <= 0)) {
            $showServiceCharge = 'no';
        }
        if ($showServiceCharge == 'no' && $showTDS == 'no') {
            $showAmountPayable = 'no';
        }
        $commission_types = $ewalletService->getEnabledBonuses($module_status, $this->compensation());
        $received_commission = [];
        $commission = $legAmount->with(['user' => function ($qry) {
            $qry->select('username', 'id', 'delete_status')->where('user_type', '!=', 'employee');
        }, 'user.userDetail:name,second_name,user_id,id'],)->paginate(request()->per_page ?? 10)->withQueryString();

        $totalPages = $commission->lastPage();
        if ($commission->currentPage() > $totalPages) {
            // Set the current page to the last page
            $commission->setCurrentPage($totalPages);
        }

        // Set the onEachSide value
        $commission->onEachSide(1);
        $currency = currencySymbol();

        return view('admin.report.commission-report', compact('showTDS', 'showServiceCharge', 'showAmountPayable', 'commission_types', 'module_status', 'currency', 'commission', 'username'));
    }

    public function upgradePackage(PackageupgradeRequest $request)
    {
        $data = PackageUpgradeHistory::query()->with('currentPackage', 'upgradePackage', 'user.userDetail', 'paymentMethod');
        $currency = currencySymbol();
        $username = null;
        if ($request->has('username') && $request->username != '') {
            $username = User::select('id', 'username')->whereKey($request->username)->first();
            $data = $data->where('user_id', $request->username);
        }
        $moduleStatus = $this->moduleStatus();
        if ($request->has('package') && $request->package != '') {
            if ($moduleStatus->ecom_status)
            {
                $data = $data->where('oc_current_package_id', $request->package);
            }
            else
            {
                $data = $data->where('current_package_id', $request->package);
            }
        }
        $packageUpgrades = $data->paginate(10)->withQueryString();
        if ($moduleStatus->ecom_status) {
            $packages = OCProduct::where('package_type', 'registration')->select('product_id as id', 'model as name')->get();
        } else {
            $packages = Package::ActiveRegPackage()->select('id', 'name')->get();
        }

        return view('admin.report.package-upgrade-historyReport', compact('packageUpgrades', 'packages', 'username', 'currency', 'moduleStatus'));
    }

    public function rankAchieversReport(RankAchieversRequest $request)
    {
        $ranks = Rank::select('name', 'id')->get();
        $data = RankUser::query()->with('user', 'user.userDetails', 'rank');
        if ($request->has('rank') && $request->rank != '') {
            $data = $data->whereIn('rank_id', [...$request->rank]);
        }
        $month = now()->month;
        $year = now()->year;
        $day = now()->day;
        switch ($request->date) {
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

        if ($request->date == 'custom' && $request->fromDate != null && $request->toDate != null) {
            $data->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
        }

        $rankAchievers = $data->paginate(10)->withQueryString();

        return view('admin.report.rank-achieversReport', compact('rankAchievers', 'ranks'));
    }

    public function rankPerformanceReport(Request $request)
    {
        $criteria = [
            'referal_count' => false,
            'joinee_package' => false,
            'personal_pv' => false,
            'group_pv' => false,
            'downline_count' => false,
            'downline_package_count' => false,
            'downline_rank' => false,
        ];
        $downlinePackageCount = $downlineRankCount = null;

        $activeCriteria = RankConfiguration::Active()->get();
        $criteriaDetails = Package::ActiveRegPackage()->with('rank')->get();
        $username = null;
        $user = User::query();
        if ($request->has('username')) {
            $user->whereKey($request->username);
        } else {
            $user->where('user_type', 'admin')->with('rankDetail', 'sponsor');
        }

        $module_status = $this->moduleStatus();
        if ($activeCriteria->contains('slug', 'referral-count')) {
            $criteria['referal_count'] = true;
            $user->withCount('sponsorDescendant');
        }
        if ($activeCriteria->contains('slug', 'joiner-package')) {
            $criteria['joinee_package'] = true;
            $user->with('package');
        }
        if ($activeCriteria->contains('slug', 'personal-pv')) {
            $criteria['personal_pv'] = true;
        }
        if ($activeCriteria->contains('slug', 'group-pv')) {
            $criteria['group_pv'] = true;
        }
        if ($activeCriteria->contains('slug', 'downline-member-count') && in_array($module_status->mlm_plan, ['Binary', 'Matrix'])) {
            $criteria['downline_count'] = true;
            $user->withCount('descendants');
        }
        if ($activeCriteria->contains('slug', 'downline-package-count')) {
            $criteria['downline_package_count'] = true;
            $downlinePackageCount = $user->with('descendant.package')->first()->descendant
                                            ->groupBy('package.id')
                                            ->map(function ($descendants) {
                                                return [
                                                    'package' => $descendants->first()->package,
                                                    'count' => $descendants->count(),
                                                ];
                                            });
        }
        if ($activeCriteria->contains('slug', 'downline-rank-count')) {
            $criteria['downline_rank'] = true;
            $downlineRankCount = $user->with('descendant.rank')->first()->descendant
                                            ->groupBy('rankDetail.id')
                                            ->map(function ($descendants) {
                                                return [
                                                    'rank' => $descendants->first()->rankDetail,
                                                    'count' => $descendants->count(),
                                                ];
                                            });
        }
        $userData = $user->with('rankDetail', 'userDetail')->first();
        $rankUser = RankUser::query()->with('rank.rankDetails', 'user.userDetail')->where('status', '1')->where('user_id', $userData->id)->first();
        $nextRank = null;
        if ($rankUser) {
            $nextRank = Rank::where('id', '>', $rankUser->rank_id)->with('rankDetails', 'rankCriteria')->get();
        }
        return view('admin.report.rank-performanceReport', compact('rankUser', 'nextRank', 'criteria', 'userData', 'criteriaDetails', 'activeCriteria', 'downlineRankCount', 'downlinePackageCount'));
    }

    public function topearners()
    {
        $currency = currencySymbol();
        $reportService = new ReportService;
        $data = $reportService->getTopEarnersReport();
        $topEarners = $data->paginate(10)->withQueryString();

        return view(
            'admin.report.top-earners',
            compact('topEarners', 'currency')
        );
    }

    public function payoutReport(Request $request)
    {
        $currency = currencySymbol();
        $reportService = new ReportService;
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
                    $data = $reportService->getPayoutPendingReports($request);
                    $amountPaids = $data->latest('id')->paginate(10)->withQueryString();

                    return view('admin.report.payout-pendingReport', compact('amountPaids', 'currency'));
                }
                if ($payoutSettings['payoutType'] == 'from_ewallet' || $payoutSettings['payoutType'] == 'both') {
                    $data = $reportService->getUserAmountPendingReport($request, $configuration);
                    $amountPaids = $data->latest('id')->paginate(10)->withQueryString();

                    return view('admin.report.userAmount-pendingReport', compact('amountPaids', 'currency'));
                }
            }
        }

        $data = $reportService->getReleasePayouts($request);

        $amountPaids = $data->latest('id')->paginate(10)->withQueryString();

        return view('admin.report.payout-releaseReport', compact('amountPaids', 'currency'));
    }

    public function totalBonusReport()
    {
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

        return view('admin.report.total-bonus', compact('showTDS', 'showServiceCharge', 'showAmountPayable'));
    }

    public function gettotalBonusReport(Request $request)
    {
        $totalBonus = User::Select('username', 'id')->where('user_type', '!=', 'employee')
        ->with('userDetail:id,name,second_name,user_id')
        ->whereHas('legamtDetails')
        ->withSum('legamtDetails', 'tds')
        ->withSum('legamtDetails', 'amount_payable')
        ->withSum('legamtDetails', 'service_charge')
        ->withSum('legamtDetails', 'total_amount')
        ->having('legamt_details_sum_total_amount', '>', 0);

        if ($request->has('username') && $request->username != null) {
            $totalBonus->whereKey($request->username);
        }

        $currency = currencySymbol();

        $month = now()->month;
        $year = now()->year;
        $day = now()->day;
        $today = 'today';
        switch ($request->dateRange) {
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

        if ($request->dateRange == 'custom' && $request->from != null && $request->to != null) {
            $totalBonus->whereRelation('legamtDetails', function (Builder $query) use ($request) {
                $query->whereDate('created_at', '>=', $request->from)->whereDate('created_at', '<=', $request->to);
            });
        }
        $count = $totalBonus->count();

        $res = $totalBonus->offset($request->start)->limit($request->length)->get();

        $data = [];

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
        foreach ($res as $key => $user) {
            $data[] = [
                'index' => $request->start + $key + 1,
                'member' => '<div class="d-flex"><img class="ht-30 img-transaction" src="' . asset('/assets/images/users/avatar-1.jpg') . '" style="max-width:50px;"><br><div class="transaction-user"><h5>' . $user->userDetail->name . '</h5><span>' . $user->userDetail->second_name . '(' . $user->username . ')' . '</span></div></div>',
                'total_amount' => $currency . ' ' . formatCurrency($user->legamt_details_sum_total_amount),
                'tds' => ($showTDS) ? $currency . ' ' . formatCurrency($user->legamt_details_sum_tds) : 0,
                'service_charge' => ($showServiceCharge) ? $currency . ' ' . formatCurrency($user->legamt_details_sum_service_charge) : 0,
                'amount_payable' => ($showAmountPayable) ? $currency . ' ' . formatCurrency($user->legamt_details_sum_amount_payable) : 0,
            ];
        }

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]);
    }

    public function checkRegistrationFeeDisplay()
    {
        $currentRegFee = $this->configuration()['reg_amount'];
        $totalRegFee = UsersRegistration::sum('reg_amount');

        return $currentRegFee <= 0 && $totalRegFee <= 0 ? false : true;
    }

    public function getinvoiceDetails($id)
    {
        $model      = AmountPaid::with('user.userDetail')->findOrfail($id);
        $currency   = currencySymbol();
        $view       = view('admin.reports.invoice', compact('model', 'currency'))->render();
        return response()->json([
            'status'    => true,
            'data'      => $view
        ]);
    }

    public function subscriptionReport()
    {
        $moduleStatus = $this->moduleStatus();
        return view('admin.report.subscription-report', compact('moduleStatus'));
    }

    public function getSubscriptionReport(Request $request)
    {
        try {
            $service = new ReportService;
            $reports = $service->getSubscriptionReport($request);
            $currency = currencySymbol();
            $moduleStatus = $this->moduleStatus();

            return DataTables::of($reports)
                ->addIndexColumn()
                ->addColumn('member', function ($data) {
                    return '<div class="d-flex"><img class="ht-30 img-transaction" src="' . asset('/assets/images/users/avatar-1.jpg') . '" style="max-width:50px;"><br><div class="transaction-user"><h5>' . $data->user->userDetail->name . '</h5><span>' . $data->user->userDetail->second_name . '(' . $data->user->username . ')' . '</span></div></div>';
                })
                ->addColumn('package', function ($data) use ($moduleStatus, $currency) {
                    return $data->package->name ?? $data->package->model ?? 'NA' . '(' . $currency .  formatCurrency($data->price ?? 0) . ')';
                })
                ->editColumn('payment_method', fn ($data) => $data->paymentMethod->name ?? "N/A" )
                ->editColumn('total_amount', function ($data) use ($currency) {
                    return "<span class='badge-amount'>" . $currency . " " . formatCurrency($data->total_amount) . "</span>";
                })
                ->editColumn('created_at', fn ($data) => Carbon::parse($data->created_at)->format('M d, Y, h:ia'))
                ->with('sum', function () use ($reports, $currency) {
                    return $currency . " " . formatCurrency($reports->sum('total_amount'));
                })
                ->rawColumns(['member', 'payment_method', 'created_at', 'total_amount'])
                ->make(true);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function purchaseReport()
    {
        $currency = currencySymbol();
        return view('admin.report.purchase-report', compact('currency'));
    }

    public function getPurchaseReport(Request $request)
    {
        $tSum = 0;
        $moduleStatus   = $this->moduleStatus();
        try {
            $service = new ReportService;
            $orders = $service->getPurchaseReports($request, $moduleStatus->ecom_status);
            // dd($orders->get()[0]);
            $currency = currencySymbol();

            return DataTables::of($orders)
                ->addIndexColumn()
                ->editColumn('invoice_no', function ($data) use($moduleStatus){
                    $invoiceId = ($moduleStatus->ecom_status) ? $data->order_id : $data->id;
                    return '<a href="' . route("report.getPurchaseInvoice", $invoiceId) . '" onclick="getInvoice(this)">INV-' . $invoiceId . '</a>';
                })
                ->addColumn('member', function ($data) {
                    return '<div class="d-flex"><img class="ht-30 img-transaction" src="' . asset('/assets/images/users/avatar-1.jpg') . '" style="max-width:50px;"><br><div class="transaction-user"><h5>' . $data->user->userDetail->name . '</h5><span>' . $data->user->userDetail->second_name . '(' . $data->user->username . ')' . '</span></div></div>';
                })
                ->editColumn('payment_method', function ($data) use($moduleStatus) {
                    if($moduleStatus->ecom_status) {
                        $paymentMethod = $data->payment_method;
                    } else {
                       $paymentMethod =  ($data->paymentMethod?->slug == 'free-joining') ? 'Free Purchase' : $data->paymentMethod?->name;
                    }
                    return $paymentMethod;
                })
                ->editColumn('total_amount', function ($data) use ($currency, $moduleStatus) {
                    if($moduleStatus->ecom_status) {
                        $amount = $data->total;
                    } else {
                        $amount = $data->total_amount;
                    }
                    return "<span class='badge-amount'>" . $currency . " " . formatCurrency($amount) . "</span>";
                })
                ->editColumn('order_date', fn ($data) => Carbon::parse($data->created_at)->format('M d Y, h:i:s A'))
                ->addColumn('tAmount', function ($data) use ($tSum) {
                    $tSum = $tSum + $data->total_amount;
                    return formatCurrency($tSum);
                })
                ->rawColumns(['invoice_no', 'member', 'payment_method', 'order_date', 'total_amount', 'package'])
                ->make(true);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    public function getSalesInvoice($id)
    {

        $moduleStatus   = $this->moduleStatus();
        try {
            if($moduleStatus->ecom_status)
            {
            $data = OcOrder::query();
            $data->with('user', 'orderDetails');
            $data = $data->find($id);
            }
            else
            {
            $userid = SalesOrder::where('invoice_no', $id)->value('user_id');
            $data = User::with('userDetail:name,second_name,mobile,user_id,id', 'salesOrder.package:name,id', 'salesOrder.paymentMethod:name,slug,id')->find($userid);
            }
            $currency = currencySymbol();
            $moduleStatus = $this->moduleStatus();
            $view = view('admin.report.ajax.salesInvoice', compact('data', 'currency', 'moduleStatus'));

            return response()->json([
                'status' => true,
                'data' => $view->render(),
            ]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getPurchaseInvoice($orderid)
    {
        try {
            $moduleStatus = $this->moduleStatus();
            if($moduleStatus->ecom_status) {
                $order = OcOrder::query();
                $order->with('user', 'orderDetails');
            } else {
                $order = Order::query();
                $order->with('user', 'orderDetails:quantity,amount,id,order_id,package_id', 'orderDetails.package:name,id', 'paymentMethod', 'address');
            }
            $order = $order->find($orderid);
            // dd($order);
            $currency = currencySymbol();
            $view = view('admin.report.ajax.purchaseInvoice', compact('order', 'currency', 'moduleStatus'));

            return response()->json([
                'status' => true,
                'data' => $view->render(),
            ]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
