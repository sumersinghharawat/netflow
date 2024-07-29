<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\CoreInfController;
use App\Models\LegAmount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommissionController extends CoreInfController
{
    public function commissionReport(Request $request)
    {
        $users = User::with('userDetails', 'legamountDetails')->where('active', 'yes')->where('user_type', '!=', 'admin')->get();

        $userIds = $users->pluck('id');
        $request->date_range = 'this_year';
        if ($request->date_range == 'custom') {
            if ($request->from_date || $request->to_date) {
                if ($request->from_date) {
                    $custom_from_operator = '>=';
                    $custom_from_value = $request->from_date;
                }
                if ($request->to_date) {
                    $custom_to_operator = '<=';
                    $custom_to_value = $request->to_date;
                }
            }
        }
        if ($request->date_range == 'overall') {
            $first_user = $users->first();
            $last_user = $users->last();
            $overall_from_operator = '>=';
            $overall_from_value = $first_user->date_of_joining;
            $overall_to_operator = '<=';
            $overall_to_value = $last_user->date_of_joining;
        }
        if ($request->date_range == 'today') {
            $today_from_operator = 'LIKE';
            $today_from_value = now();
            $today_to_operator = 'LIKE';
            $today_to_value = now();
        }

        if ($request->date_range == 'this_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();

            $month_from_operator = 'LIKE';
            $month_from_value = $start;
            $month_to_operator = 'LIKE';
            $month_to_value = $end;
        }
        if ($request->date_range == 'this_year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now();
            $year_from_operator = '>=';
            $year_from_value = $start;
            $year_to_operator = '<=';
            $year_to_value = $end;
        }

        if ($request->username) {
            if (User::where('username', $request->username)->exists()) {
                $currentId = User::where('username', $request->username)->first()->id;
            }
            $users = $users->where('id', $currentId)->first();
        }
        $users = User::with('userDetails', 'legamountDetails')->where('active', 'yes')
                                   ->where('user_type', '!=', 'admin')
                                   ->where('date_of_joining', $year_from_operator, $year_from_value)
                                   ->orwhere('date_of_joining', $year_to_operator, $year_to_value)

        ->get();

        $showTDS = 'yes';
        $showServiceCharge = 'yes';
        $showAmountPayable = 'yes';
        $configuration = $this->configuration();
        $compensation = $this->compensation();
        $performance_bonus_status = $compensation->performance_bonus;

        if (($configuration->tds <= 0) && (LegAmount::sum('tds') <= 0)) {
            $showTDS = 'no';
        }
        if (($configuration->service_charge <= 0) && (LegAmount::sum('service_charge') <= 0)) {
            $showServiceCharge = 'no';
        }
        if ($showServiceCharge == 'no' && $showTDS == 'no') {
            $showAmountPayable = 'no';
        }
        $commission_types = $this->getEnabledBonusList();
        $received_commission = $this->getReceivedBonusList();

        $commission_types = array_unique(array_merge($commission_types, $received_commission));

        $users = User::with('userDetails', 'legamountDetails')->where('active', 'yes')->get();

        return view('admin.report.commission-report', compact('users', 'commission_types'));
    }
}
