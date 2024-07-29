<?php

namespace App\Http\Controllers\Report;

use App\Exports\ActiveDeactiveExcel;
use App\Exports\EpinTransferExport;
use App\Exports\JoinReport;
use App\Exports\PayoutPendingUserAmountExport;
use App\Exports\PayoutPendingUserRequestExport;
use App\Exports\PayoutReleaseExport;
use App\Exports\ProfileDateReport;
use App\Exports\PurchaseExport;
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

class CsvController extends Controller
{
    public function exportActiveDeactiveCsv(Request $request)
    {
        $reportService = new ReportService;

        $reportData =  $reportService->activateReport($request);

        return Excel::download(new ActiveDeactiveExcel($reportData), 'ActiveDeactive.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportRankAchieversCsv(Request $request)
    {
        $fileName = 'RankAchiever.csv';
        $serviceClass = new ReportService;
        $reportData = $serviceClass->rankAchieversReport($request);
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Membername', 'New Rank', 'Date'];

        $callback = function () use ($reportData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reportData as $rank) {
                $data['name'] = $rank->user->userDetails->name . '' . $rank->user->userDetails->second_name . '' . '(' . $rank->user->username . ')';

                $data['rank_name'] = $rank->rank->name;
                $data['date'] = $rank->created_at;

                fputcsv($file, [$data['name'], $data['rank_name'], $data['date']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exporttopearnersCsv(Request $request)
    {
        $reportService = new ReportService;

        $reportData =  $reportService->getTopEarnersReport();

        return Excel::download(new TopEarnersReport($reportData->get()), 'TopEarners.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPackageUpgradeCsv(Request $request)
    {
        $fileName = 'PackageUpgrade.csv';
        $serviceClass = new ReportService;
        $data = $serviceClass->upgradePackage($request);
        $reportData = $data->get();
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Membername', 'Old Package', 'Upgraded Package', 'Amount', 'Payment Method', 'Date'];

        $callback = function () use ($reportData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reportData as $upgrade) {
                $data['name'] = $upgrade->user->userDetails->name . '' . $upgrade->user->userDetails->second_name . '' . '(' . $upgrade->user->username . ')';

                $data['current_package'] = $upgrade->currentPackage->name;
                $data['upgrade_package'] = $upgrade->upgradePackage->name;
                $data['payment_amount'] = $upgrade->payment_amount;
                if ($upgrade->payment_type == 'free_upgrade' && $upgrade->payment_amount == 0) {
                    $data['payment_type'] = 'Manualy by admin';
                } elseif ($upgrade->payment_type == 'free_upgrade' && $upgrade->payment_amount != 0) {
                    $data['payment_type'] = 'Free upgrade';
                } else {
                    $data['payment_type'] = $upgrade->payment_type;
                }

                $data['date'] = $upgrade->created_at;

                fputcsv($file, [$data['name'], $data['current_package'], $data['upgrade_package'], $data['payment_amount'], $data['payment_type'], $data['date']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportJoiningReportCsv(Request $request)
    {
        $fileName = 'JoiningReport.csv';
        $serviceClass = new ReportService;
        $reportData = $serviceClass->joinReport($request);
        $coreController = new ReportController();
        $regFeeStatus = $coreController->checkRegistrationFeeDisplay();
        return Excel::download(new JoinReport($reportData, $regFeeStatus), 'JoiningReport.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }



    public function exportCommissionReportCsv(Request $request)
    {
        $fileName = 'CommissionReport.csv';
        $serviceClass = new ReportService;
        $reportData = $serviceClass->commissionReport($request);

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Membername', 'Type', 'Amount', 'Tax', 'Service Charge', 'Amount Payable', 'Date'];

        $callback = function () use ($reportData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reportData as $commissionDetails) {
                $data['name'] = $commissionDetails->userDetails->name . '' . $commissionDetails->userDetails->second_name;
                $data['type'] = str_replace('_', ' ', ucfirst($commissionDetails->amount_type ?? 'NA'));
                $data['amount'] = round($commissionDetails->total_amount, 8) ?? 'Na';
                $data['tax'] = round($commissionDetails->tds, 8) ?? 'Na';
                $data['service_charge'] = round($commissionDetails->service_charge, 8) ?? 'Na';
                $data['amount_payable'] = round($commissionDetails->amount_payable, 8) ?? 'Na';
                // $data['register'] = str_replace('_', ' ', ucfirst($joinDetails->amount_type ?? 'NA' ));
                $data['date'] = $commissionDetails->date_of_submission;

                fputcsv($file, [$data['name'], $data['type'], $data['amount'], $data['tax'], $data['service_charge'], $data['amount_payable'], $data['date']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportBonusReportCsv(Request $request)
    {
        $serviceClass = new ReportService;
        $totalReport = $serviceClass->totalBonusReport($request);
        return Excel::download(new TotalBonusReport($totalReport), 'total_bonus_report.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPayoutPendingcsv(Request $request)
    {
        $fileName = 'PayoutPendingReport.csv';
        $serviceClass = new ReportService;
        $reportData = $serviceClass->payoutReport($request);
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['MemberName', 'Amount', 'Date'];

        $callback = function () use ($reportData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reportData as $payoutDetails) {
                $data['name'] = $payoutDetails->user->userDetails->name . '' . $payoutDetails->user->userDetails->second_name . '' . '(' . $payoutDetails->user->username . ')';
                $data['amount'] = $payoutDetails->amount;
                $data['date'] = $payoutDetails->date;

                fputcsv($file, [$data['name'], $data['amount'], $data['date']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPurchaseCSV(Request $request)
    {
        $serviceClass = new ReportService;
        $purchases = $serviceClass->getPurchaseReports($request);

        return Excel::download(new PurchaseExport($purchases->get()), 'purchase_report.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportSubscriptionCSV(Request $request)
    {
        $serviceClass = new ReportService;
        $subscription = $serviceClass->getSubscriptionReport($request);

        return Excel::download(new SubscriptionReportExport($subscription->get()), 'subscription_report.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPayoutPendingManualCsv(Request $request)
    {
        $serviceClass = new ReportService;
        $config = PayoutConfiguration::first();
        $reportData   = $serviceClass->getUserAmountPendingReport($request, $config);
        return Excel::download(new PayoutPendingUserAmountExport($reportData->latest()->get()), 'payout_pending_report.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPayoutPendingUserRequestCsv(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData   = $serviceClass->getPayoutPendingReports($request);
        return Excel::download(new PayoutPendingUserRequestExport($reportData->latest()->get()), 'payout_pending_report.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPayoutReleaseCsv(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData   = $serviceClass->getReleasePayouts($request);
        return Excel::download(new PayoutReleaseExport($reportData->latest()->get()), 'payout_release_report.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportTransferReportCSV(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData   = $serviceClass->getEpinTransferReports($request);
        return Excel::download(new EpinTransferExport($reportData->latest()->get()), 'epin_transfer_report.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportProfileReport(Request $request)
    {
        $serviceClass = new ReportService;
        $reportData   = $serviceClass->profileDateReport($request);
        return Excel::download(new ProfileDateReport($reportData), 'user_profile_report.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
