<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Junges\Kafka\Facades\Kafka;
use App\Services\EwalletService;
use App\Services\BusinessService;
use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;

class HomeController extends CoreInfController
{
    protected $serviceClass;

    public function __construct(DashboardService $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    public function index()
    {
        $ewalletService         = new EwalletService;
        $moduleStatus           = $this->moduleStatus();
        $currency               = currencySymbol();
        $compensation           = $this->compensation();
        $user                   = auth()->user();
        $prefix                 = config('database.connections.mysql.prefix');
        $dashboardPermission    = [];
        $fromLogin              = false;
        if(session()->has('from_login')) {
            $fromLogin          = true;
        }
        // Dashboard permission -TODO
        if (auth()->user()->user_type == 'employee') {
            $employee = auth()->user()->load('empDashboard');
            $dashboardPermission = $employee->empDashboard()->pluck('slug')->toArray();
        }
        $ewalletBalance         = $ewalletService->getTotalEwalletBalanceOfAllUser();
        $bonusList              = $ewalletService->getEnabledBonuses($moduleStatus, $compensation);

        $businessServiceClass   = new BusinessService;
        $totalOverview          = $businessServiceClass->totalOverView($moduleStatus, $bonusList);
        $TotalIncome            = $totalOverview['income'];
        $bussinessBonus         = $totalOverview['bonus'];
        $bussinessPaid          = $totalOverview['paid'];
        $bussinessPending       = $totalOverview['pending'];

        $newUsers = User::with('userDetail', 'package')->where('id', '!=', $user->id)
            ->ActiveUsers()->limit(10)->get();
        $demoStatus = config('mlm.demo_status');

        $hash_key = config('app.hash_key');
        $key = hash_hmac('sha256', $user->username, $hash_key);

        $url = null;

        $replicaurl = config("mlm.user_replica_url") . $user->username. "/" . $key;


        $doughnutDataViewArray = [];
        $approvedAmount = $this->serviceClass->getTotalAmountApproved($user->id);
        $pendingAmount = $this->serviceClass->getTotalAmountPendingRequest($user->id);
        $doughnutDataViewArray[] = $bussinessPaid;
        $transactions = $this->serviceClass->getTransactions();
        $doughnutDataViewArray[] = $approvedAmount;
        $doughnutDataViewArray[] = $pendingAmount;
        $payoutTotalRequest      = $doughnutDataViewArray[0] + $doughnutDataViewArray[1] + $doughnutDataViewArray[2];
        $payoutPerc              = ($payoutTotalRequest > 0) ? round(($doughnutDataViewArray[0] * 100) / $payoutTotalRequest) : null;
        return view('home.dashboard', compact('ewalletBalance', 'TotalIncome', 'bussinessBonus', 'bussinessPaid', 'bussinessPending', 'newUsers', 'url', 'doughnutDataViewArray', 'moduleStatus', 'currency', 'replicaurl', 'payoutPerc', 'dashboardPermission', 'payoutTotalRequest', 'fromLogin', 'transactions'));
    }

    public function getTopRecruiters()
    {
        $topRecruters = $this->serviceClass->getTopRecruiters();

        $view = view('home.ajax.topRecruters', compact('topRecruters'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ], 200);
    }

    public function getPackageProgressData()
    {
        $packageOverView = $this->serviceClass->getPackageProgressData();
        $view = view('home.ajax.packageProgress', compact('packageOverView'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ], 200);
    }

    public function getRankData()
    {
        $rankData = $this->serviceClass->getRankData();
        $view = view('home.ajax.rankData', compact('rankData'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ], 200);
    }

    public function getIncomeBonusGraph(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $type = ($request->has('type')) ? $request->type : 'year';
        $res = $this->serviceClass->getIncomeBonusBarChartData($moduleStatus, $type);

        return response()->json([
            'status' => true,
            'data' => $res,
        ], 200);
    }

    public function getJoiningsGraph(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $type = ($request->has('type')) ? $request->type : 'year';
        $res = $this->serviceClass->getJoiningGraphData($moduleStatus, $type);
        return response()->json([
            'status' => true,
            'data' => $res,
        ], 200);
    }
    public function getRightJoiningsGraph(Request $request)
    {
        $type = ($request->has('type')) ? $request->type : 'year';

        $res = $this->serviceClass->getRightJoiningsGraph($type);
        return response()->json([
            'status' => true,
            'data' => $res,
        ], 200);
    }
    public function getLeftJoiningsGraph(Request $request)
    {
        $type = ($request->has('type')) ? $request->type : 'year';
        $res = $this->serviceClass->getLeftJoiningsGraph($type);
        return response()->json([
            'status' => true,
            'data' => $res,
        ], 200);
    }

    public function getTopEarners()
    {
        $topEarners = $this->serviceClass->getTopEarners();
        $currency   = currencySymbol();
        return response()->json([
            'status' => true,
            'data' => view('home.ajax.top-earners', compact('topEarners', 'currency'))->render(),
        ], 200);
    }
    public function getIncomeCommission()
    {
        $moduleStatus        = $this->moduleStatus();
        $incomeAndCommission = $this->serviceClass->incomeAndCommission($moduleStatus);
        $currency   = currencySymbol();
        return response()->json([
            'status' => true,
            'data' => view('home.ajax.income-commission', compact('incomeAndCommission', 'currency'))->render(),
        ], 200);
    }
}
