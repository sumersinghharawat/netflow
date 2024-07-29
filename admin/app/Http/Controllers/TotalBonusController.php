<?php

namespace App\Http\Controllers;

use App\Models\LegAmount;
use App\Models\User;
use Illuminate\Http\Request;

class TotalBonusController extends CoreInfController
{
    public function index(Request $request)
    {
        $data = LegAmount::with('userDetails');
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
            $data = $data->whereBetween('date_of_submission', [$fromDate, $toDate]);
        }
        if ($request->has('username') && $request->username != '') {
            $data = $data->where('user_id', $request->username);
        }

        $memberList = User::where('active', 'yes')->with('sponsor', 'userDetails', 'package', 'rankDetail')->paginate(10);

        $legAmount = LegAmount::with('userDetails')->get();
        $legAmount1 = $data->paginate(10);
        $totaLeg = $legAmount->sum('total_leg');
        $totaAmount = $legAmount->sum('total_amount');
        $amountPayable = $legAmount->sum('amount_payable');
        $tds = $legAmount->sum('tds');
        $serviceCharge = $legAmount->sum('service_charge');

        return view('admin.report.total-bonus', compact('legAmount1', 'totaLeg', 'totaAmount', 'amountPayable', 'tds', 'serviceCharge'));
    }
}
