<?php

namespace App\Http\Controllers;

use App\Models\AmountPaid;
use App\Models\Compensation;
use App\Models\Fundtransferdetail;
use App\Models\LegAmount;
use App\Models\Order;
use App\Models\Packagevalidityextendhistory;
use App\Models\PayoutReleaseRequest;
use App\Models\UpgradesalesOrder;
use App\Models\UsersRegistration;
use App\Services\BusinessService;
use App\Services\EwalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BusinessController extends CoreInfController
{
    protected $serviceClass;

    public function __construct(BusinessService $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    public function index(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $compensation = $this->compensation();
        $currency = currencySymbol();
        $ewalletService = new EwalletService;
        $bonusList = $ewalletService->getEnabledBonuses($moduleStatus, $compensation);
        $businessCategories = $this->serviceClass->getBusinessCategories($moduleStatus, $bonusList);
        $totalOverview = $this->serviceClass->totalOverView($moduleStatus, $bonusList);
        return view('business.index', compact('totalOverview', 'businessCategories', 'currency'));
    }

    public function businessTransaction(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $compensation = $this->compensation();
        $ewalletService = new EwalletService;
        $bonusList = $ewalletService->getEnabledBonuses($moduleStatus, $compensation);
        $type = ['income', 'bonus', 'paid', 'pending'];
        $fromDate = Carbon::parse(now())->format('Y-m-d');
        $toDate = Carbon::parse(now())->format('Y-m-d');
        $filter = [
            'start' => $request->start,
            'limit' => $request->length,
            'order' => $request->column,
            'direction' => $request->dir,
        ];
        if ($request->has('users') && count($request->users) > 0) {
            $users = $request->users;
        } else {
            $users = [];
        }

        if ($request->has('category')) {
            $category = collect($request->category);
        } else {
            $category = $this->serviceClass->getBusinessCategories($moduleStatus, $bonusList);
        }

        if ($request->has('type')) {
            $type = [...$request->type];
        }

        if ($request->has('fromDate') && $request->has('toDate')) {
            $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
            $toDate = Carbon::parse($request->toDate)->format('Y-m-d');
        }
        $currency = currencySymbol();
        $transactions = $this->serviceClass->getBusinessTransactions($users, $type, $category, $fromDate, $toDate, $moduleStatus, $bonusList, $filter, $currency);
        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => intval($transactions['count']),
            "recordsFiltered" => intval($transactions['count']),
            "data" => $transactions['data']
        ]);
    }

    public function getBusinessTransactions($users, $type, $category, $fromDate, $toDate, $module_status)
    {
        $moduleStatus = $this->moduleStatus();
        $compensation = Compensation::first();
        $ewalletService = new EwalletService;
        $bonusList = $ewalletService->getEnabledBonuses($moduleStatus, $compensation);

        if ($fromDate) {
            $fromDate = date('Y-m-d 00:00:00', strtotime($fromDate));
        }
        if ($toDate) {
            $toDate = date('Y-m-d 23:59:59', strtotime($toDate));
        }

        if (empty($type)) {
            $type = ['income', 'bonus', 'paid', 'pending'];
        }

        if (empty($category)) {
            $category = $this->serviceClass->getBusinessCategories($moduleStatus, $compensation, $bonusList);
        }

        $queryList = [];
        $regDetailsArray = [];
        $fundTransferArray = [];
        $regDetailsRegisterationArray = [];
        $orderDetailsArray = [];
        $upgradesalesArray = [];
        $validityextendArray = [];
        $fundTransferArray = [];
        $amountPaidArray = [];

        $income_categories = $this->getIncomeCategories($module_status);

        $bonus_categories = $this->getBonusCategories($module_status);

        $bonus_categories_db = $ewalletService->getEnabledBonuses($moduleStatus, $compensation);

        $list = [];
        if (empty($category)) {
            $list[] = $category->merge($income_categories);
        } else {
            array_push($list, $category);
        }

        if (! in_array('income', $type) && count($list[0]) > 0) {
            if ($module_status->ecom_status == '1') {
                //TODO
            }
        } else {
            $regDet = UsersRegistration::query();

            //   if ($category->contains('joining_fee'))
            if (in_array('joining_fee', $category)) {
                $regDetails = $regDet->whereIn('user_id', $users)->get();

                foreach ($regDetails as $details) {
                    $regDetailsArray['joining_fee'] = [
                        'member' => $details->name.''.$details->second_name,
                        'category' => 'Joining fee',
                        'amount' => $details->reg_amount,
                        'date' => Carbon::parse($details->created_at)->toDateString(),

                    ];
                }

                array_push($queryList, $regDetailsArray['joining_fee']);
                array_unique($queryList);
            }
            if ($module_status->product_status && in_array('register', $category)) {
                $regDetails1 = $regDet->whereIn('user_id', $users)->whereBetween('created_at', [$fromDate, $toDate])->get();
                foreach ($regDetails1 as $det) {
                    $regDetailsRegisterationArray['registeration'] = [
                        'member' => $det->name.''.$det->second_name,
                        'category' => 'Registeration',
                        'amount' => $det->product_amount,
                        'date' => Carbon::parse($det->created_at)->toDateString(),

                    ];
                }
                $regDetailsRegisterationArray['registeration'] = array_unique($regDetailsRegisterationArray['registeration']);

                array_push($queryList, $regDetailsRegisterationArray['registeration']);
            }
            if ($module_status->repurchase_status && in_array('purchase', $category)) {
                $orderDetails = Order::query()->with('user.userDetail')->whereIn('user_id', $users)->whereBetween('created_at', [$fromDate, $toDate])->get();
                foreach ($orderDetails as $order) {
                    $orderDetailsArray['purchase'] = [
                        'member' => $order->user->userDetail->name.''.$order->user->userDetail->second_name,
                        'category' => 'Purchase',
                        'amount' => $order->total_amount,
                        'date' => Carbon::parse($order->created_at)->toDateString(),

                    ];
                }
                $orderDetailsArray['purchase'] = array_unique($orderDetailsArray['purchase']);
                array_push($queryList, $orderDetailsArray['purchase']);
            }

            if ($module_status->package_upgrade && in_array('upgrade', $category)) {
                $upgradesales = UpgradesalesOrder::query()->with('user')->whereIn('user_id', $users)->whereBetween('created_at', [$fromDate, $toDate])->get();
                foreach ($upgradesales as $sales) {
                    $upgradesalesArray['upgrade'] = [
                        'member' => $sales->user->userDetail->name.''.$sales->user->userDetail->second_name,
                        'category' => 'Upgrade',
                        'amount' => $sales->amount,
                        'date' => Carbon::parse($sales->created_at)->toDateString(),

                    ];
                }
                $upgradesalesArray['upgrade'] = array_unique($upgradesalesArray['upgrade']);
                array_push($queryList, $upgradesalesArray['upgrade']);
            }

            if ($module_status->subscription_status && in_array('renewal', $category)) {
                $validityextend = Packagevalidityextendhistory::query()->with('user')->whereIn('user_id', $users)->whereBetween('created_at', [$fromDate, $toDate])->get();
                foreach ($validityextend as $validity) {
                    $validityextendArray['renewal'] = [
                        'member' => $validity->user->userDetail->name.''.$validity->user->userDetail->second_name,
                        'category' => 'Renewal',
                        'amount' => $validity->total_amount,
                        'date' => Carbon::parse($validity->created_at)->toDateString(),

                    ];
                }
                $validityextendArray['renewal'] = array_unique($validityextendArray['renewal']);
                array_push($queryList, $validityextendArray['renewal']);
            }

            if (in_array('fund_transfer_fee', $category)) {
                $fundTransfer = Fundtransferdetail::query()->with('user')->where('amount_type', 'user_credit')->where('trans_fee', '>', 0)->whereIn('from_id', $users)->whereBetween('created_at', [$fromDate, $toDate])->get();

                foreach ($fundTransfer as $fund) {
                    $fundTransferArray['trans_fee'] = [
                        'member' => $fund->user->userDetail->name.''.$fund->user->userDetail->second_name,
                        'category' => 'Transfer fee',
                        'amount' => $fund->amount,
                        'date' => Carbon::parse($fund->created_at)->toDateString(),

                    ];
                }
                if (! empty($fundTransferArray)) {
                    // $fundTransferArray['trans_fee'] =  array_unique($fundTransferArray['trans_fee']);
                    array_push($queryList, $fundTransferArray['trans_fee']);
                }
            }

            if (in_array('payout_fee', $category)) {
                $amountPaid = AmountPaid::query()->with('user')->whereIn('user_id', $users)->whereBetween('created_at', [$fromDate, $toDate])->get();

                foreach ($amountPaid as $amount) {
                    $amountPaidArray['payout_fee'] = [
                        'member' => $amount->user->userDetail->name.''.$amount->user->userDetail->second_name,
                        'category' => 'Payout fee',
                        'amount' => $amount->amount,
                        'date' => Carbon::parse($amount->date)->toDateString(),

                    ];
                }
                $amountPaidArray['payout_fee'] = array_unique($amountPaidArray['payout_fee']);
                array_push($queryList, $amountPaidArray['payout_fee']);
            }
            if (in_array('commission_charge', $category)) {
                $legamountDet = LegAmount::query()->where('service_charge', '>', 0)->whereIn('user_id', $users)->whereBetween('created_at', [$fromDate, $toDate])->get();

                foreach ($legamountDet as $legamount) {
                    $legamountDetArray[$legamount->amount_type] = [
                        'member' => $legamount->userDetails->name.''.$legamount->userDetails->second_name,
                        'category' => $legamount->amount_type,
                        'amount' => $legamount->amount,
                        'date' => Carbon::parse($legamount->date)->toDateString(),

                    ];
                    $legamountDetArray[$legamount->amount_type] = array_unique($legamountDetArray[$legamount->amount_type]);
                    array_push($queryList, $legamountDetArray[$legamount->amount_type]);
                }
            }
        }

        //  $list1[] =  $category->merge($bonus_categories);
        foreach ($bonus_categories as $bonuscategory) {
            array_push($category, $bonuscategory);
        }

        if (in_array('bonus', $type) && count($category) > 0) {
            $split_types = [
                'pin_purchase_refund' => ['pin_purchase_refund', 'pin_purchase_delete'],
                'payout_delete' => ['payout_delete', 'payout_inactive', 'withdrawal_cancel'],
                'level_commission' => ['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'],
                'xup_commission' => ['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'],
                'leg' => ['leg', 'repurchase_leg', 'upgrade_leg'],
                'matching_bonus' => ['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'],
                'donation' => ['donation', 'purchase_donation'],
                'fund_transfer' => ['user_credit', 'user_debit'],
            ];
            $amount_types = [];
            foreach ($category as $cat) {
                if (isset($split_types[$cat])) {
                    $amount_types = [...$amount_types, ...$split_types[$cat]];
                } else {
                    $amount_types = [...$amount_types, $cat];
                }
            }

            $legamountdet1 = LegAmount::query()->whereIn('amount_type', $amount_types)->whereIn('user_id', $users)->whereBetween('created_at', [$fromDate, $toDate])->get();
            foreach ($legamountdet1 as $legamountdet) {
                $legamountdet1Array[$legamountdet->amount_type] = [
                    'member' => $legamountdet->userDetails->name.''.$legamountdet->userDetails->second_name,
                    'category' => $legamountdet->amount_type,
                    'amount' => $legamountdet->amount,
                    'date' => Carbon::parse($legamountdet->date)->toDateString(),

                ];
                $legamountdet1Array[$legamountdet->amount_type] = array_unique($legamountdet1Array[$legamountdet->amount_type]);
                array_push($queryList, $legamountdet1Array[$legamountdet->amount_type]);
            }
        }
        $paidDetail = AmountPaid::query()->with('user')->where('type', 'released');
        if (in_array('paid', $type) && in_array('paid', $category)) {
            $amountpaidDet2 = $paidDetail->where('status', '1')->whereIn('user_id', $users)->whereBetween('created_at', [$fromDate, $toDate])->get();
            foreach ($amountpaidDet2 as $amountpaiddet) {
                $amountpaidDet2Array['transfer_fee'] = [
                    'member' => $amountpaiddet->user->userDetail->name.''.$amountpaiddet->user->userDetail->second_name,
                    'category' => 'Transfer fee',
                    'amount' => $amountpaiddet->amount,
                    'date' => Carbon::parse($amountpaiddet->date)->toDateString(),

                ];
                $amountpaidDet2Array['transfer_fee'] = array_unique($amountpaidDet2Array['transfer_fee']);
                array_push($queryList, $amountpaidDet2Array['transfer_fee']);
            }
        }

        if (in_array('pending', $type) && in_array('pending', $category)) {
            $amountpaidDet3 = $paidDetail->where('status', '0')->where('payment_method', 'bank')->whereIn('user_id', $users)->whereBetween('date', [$fromDate, $toDate])->get();
            foreach ($amountpaidDet3 as $item) {
                $amountpaidDet3Array['Pending'] = [
                    'member' => $item->user->userDetail->name.''.$item->userDetails->second_name,
                    'category' => 'Pending',
                    'amount' => $item->amount,
                    'date' => Carbon::parse($item->date)->toDateString(),

                ];
                $amountpaidDet3Array['Pending'] = array_unique($amountpaidDet3Array['Pending']);
                array_push($queryList, $amountpaidDet3Array['Pending']);
            }

            $payoutReleaseDet = PayoutReleaseRequest::query()->where('status', '0')->whereIn('user_id', $users)->whereBetween('date', [$fromDate, $toDate])->get();
            foreach ($payoutReleaseDet as $payout) {
                $payoutReleaseDetArray['payout_release'] = [
                    'member' => $payout->user->userDetail->name.''.$payout->user->userDetail->second_name,
                    'category' => 'Payout release',
                    'amount' => $payout->amount,
                    'date' => $payout->date,

                ];
                $payoutReleaseDetArray['payout_release'] = array_unique($payoutReleaseDetArray['payout_release']);
                array_push($queryList, $payoutReleaseDetArray['payout_release']);
            }
        }

        // $queryList  =   array_unique($queryList);

        return $queryList;
    }

    public function getIncomeCategories($module_status)
    {
        $categories = collect([]);

        if ($module_status->ecom_status) {
            $categories->push('register', 'purchase');
        } else {
            $categories->push('joining_fee');

            if ($module_status->product_status) {
                $categories->push('register');
            }

            if ($module_status->repurchase_status) {
                $categories->push('purchase');
            }
        }
        if ($module_status->package_upgrade) {
            $categories->push('upgrade');
        }
        if ($module_status->subscription_status) {
            $categories->push('renewal');
        }
        $categories->push('fund_transfer_fee', 'commission_charge', 'payout_fee');

        return $categories;
    }

    public function getBonusCategories($moduleStatus)
    {
        $ewalletService = new EwalletService;

        $bonus_list = $ewalletService->getEnabledBonuses($moduleStatus, $compensation);
        $bonus_list = array_diff($bonus_list, ['purchase_donation', 'repurchase_level_commission', 'upgrade_level_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission', 'repurchase_leg', 'upgrade_leg', 'matching_bonus_purchase', 'matching_bonus_upgrade']);

        return $bonus_list;
    }

    public function getSumary(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $compensation = $this->compensation();
        $currency = currencySymbol();
        $ewalletService = new EwalletService;
        $bonusList = $ewalletService->getEnabledBonuses($moduleStatus, $compensation);

        $totlaIncome = $this->serviceClass->totalIncome($moduleStatus, $request, $bonusList);
        $totalBonus = $this->serviceClass->totalBonus($moduleStatus, $request, $bonusList);
        $totalPending = $this->serviceClass->totalPending($moduleStatus, $request, $bonusList);
        $totalPaid = $this->serviceClass->totalPaid($moduleStatus, $request, $bonusList);
        $view = view('business.inc.summary', compact('totlaIncome', 'bonusList', 'totalBonus', 'totalPending', 'totalPaid', 'currency'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ]);
    }
}
