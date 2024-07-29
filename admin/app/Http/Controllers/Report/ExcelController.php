<?php

namespace App\Http\Controllers\Report;

use App\Exports\ActiveDeactiveExcel;
use App\Exports\CommissionReport;
use App\Exports\EpinTransferExport;
use App\Exports\JoinReport;
use App\Exports\PackageUpgradeReport;
use App\Exports\PayoutPendingReport;
use App\Exports\PayoutPendingUserAmountExport;
use App\Exports\PayoutPendingUserRequestExport;
use App\Exports\PayoutReleaseExport;
use App\Exports\ProfileDateReport;
use App\Exports\PurchaseExport;
use App\Exports\RankAchieversReport;
use App\Exports\SubscriptionReportExport;
use App\Exports\TopEarnersReport;
use App\Exports\TotalBonusReport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ReportController;
use App\Models\PayoutConfiguration;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function exportActiveDeactiveExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData = $serviceClass->activateReport($request);
        return Excel::download(new ActiveDeactiveExcel($reportData), 'ActiveDeactiveReport.xlsx');
    }

    public function exportRankAchieversExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData = $serviceClass->rankAchieversReport($request);

        return Excel::download(new RankAchieversReport($reportData), 'RankAchieversReport.xlsx');
    }

    public function exporttopearnersExcel()
    {
        $reportService = new ReportService;
        $reportData = $reportService->getTopEarnersReport();
        return Excel::download(new TopEarnersReport($reportData->get()), 'TopEarnersReport.xlsx');
    }

    public function exportPackageUpgradeExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $data = $serviceClass->upgradePackage($request);
        $reportData = $data->get();

        return Excel::download(new PackageUpgradeReport($reportData), 'PackageUpgradeReport.xlsx');
    }

    public function joinReport(Request $request)
    {
        $serviceClass = new ReportService;
        $joinDetails = $serviceClass->joinReport($request);
        $coreController = new ReportController();
        $regFeeStatus = $coreController->checkRegistrationFeeDisplay();
        return Excel::download(new JoinReport($joinDetails, $regFeeStatus), 'UserJoiningReport.xlsx');
    }

    public function commissionReport(Request $request)
    {
        $serviceClass = new ReportService;
        $commissionDetails = $serviceClass->commissionReport($request);
        return Excel::download(new CommissionReport($commissionDetails), 'CommissionReport.xlsx');
    }

    public function profileDateReport(Request $request)
    {
        $serviceClass = new ReportService;
        $profileDetails = $serviceClass->profileDateReport($request);
        return Excel::download(new ProfileDateReport($profileDetails), 'ProfileDateReport.xlsx');
    }

    public function profileReport(Request $request)
    {
        $serviceClass = new ReportService;
        $profileDetails = $serviceClass->profileDateReport($request);
        return Excel::download(new ProfileDateReport($profileDetails), 'ProfileReport.xlsx');
    }

    public function bonusreportexcel(Request $request)
    {
        // dd($request);
        $serviceClass = new ReportService;
        $totalReport = $serviceClass->totalBonusReport($request);
        $reportData = $totalReport;

        return Excel::download(new TotalBonusReport($reportData), 'TotalBonusReport.xlsx');
    }

    public function payoutPendingExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData = $serviceClass->payoutReport($request);

        return Excel::download(new PayoutPendingReport($reportData), 'PayoutPendingReport.xlsx');
    }

    public function purchaseReportExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData = $serviceClass->getPurchaseReports($request);
        return Excel::download(new PurchaseExport($reportData->get()), 'purchase_report_.xlsx');
    }

    public function subscriptionReportExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData   = $serviceClass->getSubscriptionReport($request);
        return Excel::download(new SubscriptionReportExport($reportData->get()), 'subscription_report.xlsx');
    }

    public function payoutPendingUserAmountExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $config = PayoutConfiguration::first();
        $reportData   = $serviceClass->getUserAmountPendingReport($request, $config);
        return Excel::download(new PayoutPendingUserAmountExport($reportData->latest()->get()), 'payout_pending_report.xlsx');
    }

    public function payoutPendingUserRequestExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData   = $serviceClass->getPayoutPendingReports($request);
        return Excel::download(new PayoutPendingUserRequestExport($reportData->latest()->get()), 'payout_pending_report.xlsx');
    }

    public function payoutReleaseExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData   = $serviceClass->getReleasePayouts($request);
        return Excel::download(new PayoutReleaseExport($reportData->latest()->get()), 'payout_release_report.xlsx');
    }

    public function epinTransferReportExcel(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData   = $serviceClass->getEpinTransferReports($request);
        return Excel::download(new EpinTransferExport($reportData->latest()->get()), 'epin_transfer_report.xlsx');
    }
}
