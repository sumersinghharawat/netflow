<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EwalletHistory;
use App\Models\PerformanceBonus;
use App\Models\UserBalanceAmount;
use App\Models\Fundtransferdetail;
use Illuminate\Support\Facades\DB;
use App\Models\EwalletPaymentDetail;
use Illuminate\Support\Facades\Http;
use App\Models\Purchasewallethistory;
use App\Models\EwalletPurchaseHistory;
use App\Models\EwalletTransferHistory;
use App\Models\EwalletCommissionHistory;

class EwalletService
{
    private $URL;
    private $secret;
    
    public function __construct()
    {
        $this->URL = config('services.commission.url');
        $this->secret = config('services.commission.secret');
    }

    public function getEwalletCategories($moduleStatus, $compensation, $ewalletStatus, $earningsCategories)
    {
        $categories = collect([
            'admin_credit', 'admin_debit', 'fund_transfer',
            'payout_request', 'payout_release_manual', 'payout_delete', 'payout_fee', 'fund_transfer_fee', 'payout_release'
        ]);

        if ($moduleStatus->pin_status) {
            $categories->push('pin_purchase', 'pin_purchase_refund');
        }

        if ($ewalletStatus) {
            $categories->push('registration');

            if ($moduleStatus->ecom_status || $moduleStatus->repurchase_status) {
                $categories->push('repurchase');
            }

            if ($moduleStatus->package_upgrade) {
                $categories->push('upgrade');
            }

            if ($moduleStatus->subscription_status) {
                $categories->push('package_validity');
            }
        }
        //
        $bonusCategories = $earningsCategories;
        $categories = [...$categories, ...$bonusCategories];
        return $categories;
    }

    public function getBonusCategories($moduleStatus, $compensation)
    {
        $bonusList = $this->getEnabledBonuses($moduleStatus, $compensation);
        $bonusList = $bonusList->diff(['purchase_donation', 'level_commission', 'repurchase_level_commission', 'upgrade_level_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission', 'repurchase_leg', 'upgrade_leg', 'matching_bonus_purchase', 'matching_bonus_upgrade']);

        return $bonusList->all();
    }

    public function getEnabledBonuses($moduleStatus, $compensation)
    {
        $bonusList = collect([]);
        $levelCommissionStatus = $xupCommissionStatus = 'no';

        if ($moduleStatus->whereIn('mlm_plan', ['Matrix', 'Unilevel', 'Donation']) || $moduleStatus->sponsor_commission_status) {
            $levelCommissionStatus = 'yes';
        }

        if ($moduleStatus->xup_status && $levelCommissionStatus == 'yes') {
            $xupCommissionStatus = 'yes';
            $levelCommissionStatus = 'no';
        }
        if ($moduleStatus->referral_status) {
            $bonusList->push('referral');
        }

        if ($moduleStatus->rank_status) {
            $bonusList->push('rank_bonus');
        }

        if ($levelCommissionStatus == 'yes') {
            $bonusList->push('level_commission');
            if ($moduleStatus->repurchase_status || $moduleStatus->ecom_status) {
                $bonusList->push('repurchase_level_commission');
                $bonusList->push('sales_commission');
            }

            if ($moduleStatus->package_upgrade) {
                $bonusList->push('upgrade_level_commission');
            }
        }

        if ($xupCommissionStatus == 'yes') {
            $bonusList->push('xup_commission');

            if ($moduleStatus->repurchase_status || $moduleStatus->ecom_status) {
                $bonusList->push('xup_repurchase_level_commission');
                // $bonusList->push('sales_commission');
            }

            if ($moduleStatus->package_upgrade) {
                $bonusList->push('xup_upgrade_level_commission');
            }
        }

        if ($moduleStatus->mlm_plan == 'Binary') {
            $bonusList->push('leg');

            if ($moduleStatus->repurchase_status || $moduleStatus->ecom_status) {
                $bonusList->push('repurchase_leg');
            }
            if ($moduleStatus->package_upgrade) {
                $bonusList->push('upgrade_leg');
            }
        }
        if ($moduleStatus->mlm_plan == 'Stair_Step') {
            $bonusList->push('stair_step', 'override_bonus');
        }

        if ($moduleStatus->mlm_plan == 'Board') {
            $bonusList->push('board_commission');
        }

        if ($moduleStatus->roi_status == 'yes' || $moduleStatus->hyip_status) {
            $bonusList->push('daily_investment');
        }

        if ($moduleStatus->mlm_plan == 'Donation') {
            $bonusList->push('donation', 'purchase_donation');
        }

        $matchingBonusStatus = $compensation->matching_bonus;
        $poolBonusStatus = $compensation->pool_bonus;
        $fastStartBonusStatus = $compensation->fast_start_bonus;
        $performanceBonusStatus = $compensation->performance_bonus;

        if ($matchingBonusStatus) {
            $bonusList->push('matching_bonus');

            if ($moduleStatus->repurchase_status || $moduleStatus->ecom_status) {
                $bonusList->push('matching_bonus_purchase');
            }

            if ($moduleStatus->package_upgrade) {
                $bonusList->push('matching_bonus_upgrade');
            }
        }

        if ($poolBonusStatus) {
            $bonusList->push('pool_bonus');
        }

        if ($fastStartBonusStatus) {
            $bonusList->push('fast_start_bonus');
        }

        if ($performanceBonusStatus) {
            PerformanceBonus::all()->map(fn ($item) => $bonusList->push($item->slug));
        }
        if ($moduleStatus->mlm_plan == 'Monoline') {
            $bonusList->push('rejoin');
        }

        return $bonusList;
    }

    protected function combinedTypes()
    {
        return collect([
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
        ]);
    }

    protected function ewalletAmountTypes($moduleStatus, $compensation, $earningsCategories, $ewalletStatus)
    {
        $amountTypes = collect([]);
        $creditAmountTypes = collect([]);
        $debitAmountTypes = collect([]);

        if ($moduleStatus->pin_status) {
            $amountTypes->push('pin_purchase', 'pin_purchase_refund', 'pin_purchase_delete');
            $creditAmountTypes->push('pin_purchase_refund', 'pin_purchase_delete');
            $debitAmountTypes->push('pin_purchase');
        }

        $amountTypes->push('admin_credit', 'admin_debit', 'user_credit', 'user_debit', 'payout_request', 'payout_release_manual', 'payout_delete', 'payout_inactive', 'withdrawal_cancel', 'admin_user_debit', 'admin_user_credit', 'fund_transfer_fee', 'payout_release', 'payout_fee');
        $creditAmountTypes->push('admin_credit', 'user_credit', 'payout_delete', 'payout_inactive', 'withdrawal_cancel', 'admin_user_credit');
        $debitAmountTypes->push('admin_debit', 'user_debit', 'payout_request', 'payout_release_manual', 'admin_user_debit', 'fund_transfer_fee', 'payout_release', 'payout_fee');

        if ($ewalletStatus) {
            $amountTypes->push('registration');
            $debitAmountTypes->push('registration');

            if ($moduleStatus->ecom_status || $moduleStatus->repurchase_status) {
                $amountTypes->push('repurchase');
                $debitAmountTypes->push('repurchase');
            }
            if ($moduleStatus->package_upgrade) {
                $amountTypes->push('upgrade');
                $debitAmountTypes->push('upgrade');
            }
            if ($moduleStatus->subscription_status) {
                $amountTypes->push('package_validity');
                $debitAmountTypes->push('package_validity');
            }
        }

        $enabled_bonus_list = $earningsCategories;
        $amountTypes = collect([...$amountTypes, ...$enabled_bonus_list]);
        $creditAmountTypes = collect([...$creditAmountTypes, ...$enabled_bonus_list]);

        return [
            'amountTypes' => $amountTypes,
            'creditAmountTypes' => $creditAmountTypes,
            'debitAmountTypes' => $debitAmountTypes,
        ];
    }

    public function getEwalletReportOverview($moduleStatus, $compensation, $earningsCategories, $ewalletStatus, $from_date = null, $to_date = null)
    {
        $fromDate = ($from_date) ? Carbon::parse($from_date)->format('Y-m-d 00:00:00') : false;
        $toDate = ($to_date) ? Carbon::parse($to_date)->format('Y-m-d 00:00:00') : false;
        $walletDetails = [];

        $ewalletAmountTypes = $this->ewalletAmountTypes($moduleStatus, $compensation, $earningsCategories, $ewalletStatus);
        $combinedTypes = $this->combinedTypes();
        foreach ($ewalletAmountTypes['amountTypes'] as $type) {
            if ($ewalletAmountTypes['creditAmountTypes']->contains($type)) {
                $walletDetails[$type] = ['type' => 'credit', 'amount' => 0];
            } elseif ($ewalletAmountTypes['debitAmountTypes']->contains($type)) {
                $walletDetails[$type] = ['type' => 'debit', 'amount' => 0];
            }
            if ($combinedTypes->has($type)) {
                $walletDetails[$combinedTypes[$type]] = $walletDetails[$type];
                unset($walletDetails[$type]);
            }
        }
        // commissions data
        $commissionData = EwalletCommissionHistory::selectRaw("SUM(amount - purchase_wallet) as total, amount_type ,'credit' as type")
            ->whereIn('amount_type', $earningsCategories)
            ->groupBy('amount_type');

        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->subDay();
            $toDate = Carbon::parse($toDate)->addDay();
            $commissionData->whereBetween('date_added', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $commissionData->where('date_added', '>=', $fromDate);
        } elseif ($toDate) {
            $commissionData->where('date_added', '<=', $toDate);
        }
        $ewalletHistories = $commissionData->get();
        foreach ($ewalletHistories as $ewalletHistory) {
            if ($combinedTypes->has($ewalletHistory['amount_type'])) {
                $ewalletHistory['amount_type'] = $combinedTypes[$ewalletHistory['amount_type']];
                $walletDetails[$ewalletHistory['amount_type']]['type'] = $ewalletHistory['type'];
            }
            $walletDetails[$ewalletHistory['amount_type']]['amount'] += $ewalletHistory->total;
        }

        // fund transfer data
        $fundTransferData = EwalletTransferHistory::selectRaw("SUM(amount) as total, amount_type , type")
            ->whereIn('amount_type', $ewalletAmountTypes['amountTypes'])
            ->groupBy('amount_type', 'type');
        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->subDay();
            $toDate = Carbon::parse($toDate)->addDay();
            $fundTransferData->whereBetween('date_added', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $fundTransferData->where('date_added', '>=', $fromDate);
        } elseif ($toDate) {
            $fundTransferData->where('date_added', '<=', $toDate);
        }
        $ewalletHistories2 = $fundTransferData->get();
        foreach ($ewalletHistories2 as $ewalletHistory) {
            if ($combinedTypes->has($ewalletHistory['amount_type'])) {
                $ewalletHistory['amount_type'] = $combinedTypes[$ewalletHistory['amount_type']];
                $walletDetails[$ewalletHistory['amount_type']]['type'] = $ewalletHistory['type'];
            }
            $walletDetails[$ewalletHistory['amount_type']]['amount'] += $ewalletHistory->total;
        }

        // transfer fee
        $transactionFee = EwalletTransferHistory::select('amount_type', 'type', DB::raw('SUM(transaction_fee) as fee'), DB::raw('SUM(amount) as totalamount'))
            ->groupBy('amount_type', 'type')
            ->whereIn('amount_type', ['user_credit', 'admin_user_credit']);

        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->subDay();
            $toDate = Carbon::parse($toDate)->addDay();
            $transactionFee->whereBetween('date_added', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $transactionFee->where('date_added', '>=', $fromDate);
        } elseif ($toDate) {
            $transactionFee->where('date_added', '<=', $toDate);
        }
        $feeData    = $transactionFee->get();
        foreach ($feeData as $key => $fee) {
            if (in_array($fee->amount_type, ['user_credit', 'admin_user_credit'])) {
                $walletDetails['fund_transfer_fee']['type'] = 'debit';
                $walletDetails['fund_transfer_fee']['amount'] += $fee->fee;
            }
        }

        // other transaction
        $purchaseData = EwalletPurchaseHistory::select('amount_type', 'type', 'transaction_fee', DB::raw('SUM(amount) as total',))
            ->groupBy('amount_type', 'type', 'transaction_fee');
        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->subDay();
            $toDate = Carbon::parse($toDate)->addDay();
            $purchaseData->whereBetween('date_added', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $purchaseData->where('date_added', '>=', $fromDate);
        } elseif ($toDate) {
            $purchaseData->where('date_added', '<=', $toDate);
        }
        $purchaseDetailsAmount = $purchaseData->get();
        foreach ($purchaseDetailsAmount as $key => $fee) {
            if ($combinedTypes->has($fee['amount_type'])) {
                $fee['amount_type'] = $combinedTypes[$fee['amount_type']];
                $walletDetails[$fee['amount_type']]['type'] = $fee['type'];
            }
            $walletDetails[$fee['amount_type']]['amount'] += $fee->total;
            // payout fee is a separate entry in the db, so no need to of this.

            // if ($fee['amount_type'] == 'payout_request' || $fee['amount_type'] == 'payout_release_manual') {
            //     $walletDetails['payout_fee'] = [
            //         'type' => 'debit',
            //         'amount' => $fee['transaction_fee'] ?? 0,
            //     ];
            // }

        }
        return collect($walletDetails);
    }

    public function purchaseWalletBalance($moduleStatus)
    {
        if (!$moduleStatus->purchase_wallet) {
            return 0;
        }
        $credit_sum = Purchasewallethistory::where('type', 'credit')
            ->sum('purchase_wallet');
        $debit_sum = Purchasewallethistory::where('type', 'debit')
            ->where('amount_type', '!=', 'payout_release')
            ->sum('purchase_wallet');

        return $credit_sum - $debit_sum;
    }

    public function getPreviousPurchasewalletBalance(Request $request)
    {
        $data = Purchasewallethistory::query();
        if ($request->has('users') && count($request->users) > 0) {
            $data->whereHas('user', fn ($user) => $user->whereIn('id', [...$request->users]));
        } else {
            $data->whereHas('user', fn ($user) => $user->whereIn('id', [auth()->user()->id]));
        }

        $data->select(DB::raw("SUM(IF(type = 'debit' AND amount_type != 'payout_release', purchase_wallet, 0)) as debit"), DB::raw("SUM(IF(type = 'credit', purchase_wallet, 0)) as credit"));

        $purchasewallet = $data->with('fromuser', 'user')->first();
        $data = $purchasewallet->credit ?? 0 - $purchasewallet->debit ?? 0;

        return $data;
    }

    public function walletStatementDescription($item, $moduleStatus, $user, $admin)
    {
        $fromUserAmountTypes = collect([
            'referral',
            'level_commission',
            'repurchase_level_commission',
            'upgrade_level_commission',
            'xup_commission',
            'xup_repurchase_level_commission',
            'xup_upgrade_level_commission',
            'sales_commission',
        ]);
        $currency = currencySymbol();
        $description = '';
        if ($item->ewallet_type == 'fund_transfer') {
            if ($item->amount_type == 'user_credit') {
                $description = __('ewallet.transfer_from_to', ['touser' => $item->user->username, 'username' => $item->from_user]);
            } elseif ($item->amount_type == 'user_debit') {
                if ($item->from_id == $user->id) {
                    $description = __('ewallet.fund_transfer_to', ['username' => $item->user->username]);
                } else {
                    $description = __('ewallet.fund_transfer_from', ['fromuser' => $item->user->username]);
                }
            } elseif ($item->amount_type == 'admin_credit') {
                if ($item->user_id == $user->id) {
                    $description = __('ewallet.credited_by', ['username' => $admin->username]);
                } else {
                    $description = __('ewallet.credited_by_to', ['username' => $item->user->username]);
                }
            } elseif ($item->amount_type == 'admin_debit') {
                $description = __('ewallet.deducted_by', ['username' => $admin->username]);
            } elseif($item->amount_type == 'admin_user_debit') {
                $description = __('ewallet.transfer_from_to', ['touser' => $item->details->toUser->username, 'username' => $item->from_user]);
            } elseif($item->amount_type == 'admin_user_credit') {
                $description = __('ewallet.transfer_from_to', ['touser' => $item->details->toUser->username, 'username' => $item->from_user]);
            }  elseif ($item->amount_type == 'payout_delete') {
                $description = __('ewallet.credited_for_payout_requestdelete');
            } elseif ($item->amount_type == 'withdrawal_cancel') {
                $description = __('ewallet.credited_for_waiting_withdrawal_cancel');
            } elseif ($item->amount_type == 'pin_purchase') {
                $description = __('ewallet.deducted_for_pin_purchase');
            } elseif ($item->amount_type == 'pin_purchase_refund') {
                $description = __('ewallet.credited_for_pin_purchase_refund');
            } elseif ($item->amount_type == 'pin_purchase_delete') {
                $description = __('ewallet.credited_for_pin_purchase_delete');
            }
        } elseif ($item->ewallet_type == 'commission') {
            if ($item->amount_type == 'donation') {
                $description = __('ewallet.donation_credit', ['username' => $item->from_user]);
                if ($item->type == 'debit') {
                    $description = __('ewallet.donation_debit', ['username' => $item->from_user]);
                }
            } elseif ($item->amount_type == 'board_commission' && $moduleStatus->table_status) {
                $description = 'Table commission';
            } else {
                if ($fromUserAmountTypes->contains($item->amount_type)) {
                    $description = __('ewallet.' . $item->amount_type . '_from', ['username' => $item->from_user]);
                } else {
                    $description = __('ewallet.' . $item->amount_type);
                }
            }
        } elseif ($item->ewallet_type == 'ewallet_payment') {
            if ($item->amount_type == 'registration') {
                $description = __('ewallet.deducted_for_registration');
            } elseif ($item->amount_type == 'repurchase') {
                $description = __('ewallet.deducted_for_repurchase_by', ['username' => $item->user->username]);
            } elseif ($item->amount_type == 'package_validity') {
                $description = __('ewallet.deducted_for_membership_renewal_of', ['username' => $item->user->username]);
            } elseif ($item->amount_type == 'upgrade') {
                $description = __('ewallet.deducted_for_upgrade_of', ['username' => $item->user->username]);
            }
        } elseif ($item->ewallet_type == 'payout') {
            if ($item->amount_type == 'payout_request') {
                $description = __('ewallet.deducted_for_payout_request');
            } elseif ($item->amount_type == 'payout_release') {
                $description = __('ewallet.payout_released_for_request');
            } elseif ($item->amount_type == 'payout_delete') {
                $description = __('ewallet.credited_for_payout_requestdelete');
            } elseif ($item->amount_type == 'payout_release_manual') {
                $description = __('ewallet.payout_released_by_manual');
            } elseif ($item->amount_type == 'withdrawal_cancel') {
                $description = __('ewallet.credited_for_waiting_withdrawal_cancel');
            } elseif ($item->amount_type == 'payout_fee') {
                $description = __('ewallet.payout_fee');
            }
        } elseif ($item->ewallet_type == 'pin_purchase') {
            $description = __('ewallet.deducted_for_pin_purchase');
            if ($item->amount_type == 'pin_purchase_refund') {
                $description = __('ewallet.credited_for_pin_purchase_refund');
            } elseif ($item->amount_type == 'pin_purchase_delete') {
                $description = __('ewallet.credited for pin purchase delete');
            }
        } elseif ($item->ewallet_type == 'package_purchase') {
            $description = __('ewallet.purchase_donation_from', ['username' => $item->user->username]);
        }

        if ($item->pending_id) {
            $description .= '<span>' . 'Pending' . '</span>';
        }

        if (in_array($item->ewallet_type, ['fund_transfer', 'payout']) && $item->transaction_fee > 0 && $item->type == 'debit') {
            $description .= ' (' . 'Transaction fee ' . $currency . ' ' . formatCurrency($item->transaction_fee) . ')';
        }

        return $description ?? '';
    }

    public function walletStatementBalance($item, $balance)
    {
        if ($item->type == 'debit' && $item->amount_type != 'payout_release') {
            $balance = $balance - $item->amount - $item->transaction_fee;
        } elseif ($item->type == 'credit') {
            $balance = $balance + $item->amount - $item->purchase_wallet;
        }

        return $balance;
    }

    public function updateFromUserBalance($user, $amount)
    {
        $currentBalance = $user->userBalance->balance_amount;
        $newBalance = $currentBalance - $amount;
        $user->userBalance()->update(['balance_amount' => $newBalance]);
    }

    public function updateToUserBalance($user, $amount)
    {
        $currentBalance = $user->userBalance->balance_amount;
        $newBalance = $currentBalance + $amount;
        $user->userBalance()->update(['balance_amount' => $newBalance]);
    }

    public function insertTransactionDetails($from, $to, $data, $fee, $transactionId, $moduleStatus, $type = '')
    {
        if ($moduleStatus->employee_status && auth()->user()->user_type == 'employee') {
            $from = User::GetAdmin();
        }

        $fundTransfer = new Fundtransferdetail;
        if ($type != '') {
            $fundTransfer->from_id = $from->id;
            $fundTransfer->to_id = $to->id;
            $fundTransfer->amount = $data['amount'];
            $fundTransfer->notes = $data['notes'];
            $fundTransfer->amount_type = $type;
            $fundTransfer->trans_fee = $fee;
            $fundTransfer->transaction_id = $transactionId->id;
        } else {
            $fundTransfer->from_id = $from->id;
            $fundTransfer->to_id = $to->id;
            $fundTransfer->amount = $data['amount'];
            $fundTransfer->notes = $data['notes'];
            $fundTransfer->amount_type = 'user_credit';
            $fundTransfer->trans_fee = $fee;
            $fundTransfer->transaction_id = $transactionId->id;
        }
        $fundTransfer->save();

        return $fundTransfer;
    }

    public function addToEwalletHistory($moduleStatus, $from, $to, $reference, $amount, $amountType, $transType, $transactionId, $fee, $notes, $walletType)
    {
        $currentBalance = $to->userBalance()->first()->balance_amount ?? 0;
        // $currentBalance = UserBalanceAmount::select('balance_amount')->whereKey($to->id)->first()->balance_amount ?? 0;
        if ($amountType == 'admin_credit') {
            $fromBalance    = $from->userBalance->balance_amount;
            $balance        = $currentBalance + $amount;
            $from           = null;
        } elseif ($amountType == 'admin_debit') {
            $fromBalance    = $from->userBalance->balance_amount;
            $balance        = $currentBalance - $amount;
            $from           = null;
        } elseif ($amountType == 'user_credit') {
            $fromBalance    = $from->userBalance->balance_amount - ($amount + $fee);
            $balance        = $currentBalance + $amount;
            $from           = $from->id;
        } elseif ($amountType == 'user_debit') {
            $fromBalance    = $from->userBalance->balance_amount - ($amount + $fee);
            $balance        = $currentBalance + $amount;
            $from           = $from->id;
        } elseif ($amountType == 'payout_release') {
            $fromBalance    = 0;
            $balance        = $currentBalance;
            $from           = null;
        } else {
            if ($transType == "credit") {
                $fromBalance    = ($from) ? $from->userBalance->balance_amount - $amount : 0;
                $balance        = $currentBalance + $amount;
            } else {
                $fromBalance    = ($from) ? $from->userBalance->balance_amount - $amount : 0;
                $balance        = $currentBalance - $amount;
            }
            $from           = ($from) ? $from->id : null;
        }

        $ewalletHistory                     = new EwalletHistory;
        $ewalletHistory->user_id            = $to->id ?? $to;
        $ewalletHistory->from_id            = $from;
        $ewalletHistory->ewallet_type       = $walletType;
        $ewalletHistory->amount             = $amount;
        $ewalletHistory->balance            = $balance;
        $ewalletHistory->from_balance       = $fromBalance;
        $ewalletHistory->amount_type        = $amountType;
        $ewalletHistory->type               = $transType;
        $ewalletHistory->date_added         = now();
        $ewalletHistory->transaction_id     = $transactionId;
        $ewalletHistory->reference_id       = $reference;
        $ewalletHistory->transaction_fee    = $fee;
        $ewalletHistory->transaction_note   = $notes;
        $ewalletHistory->save();

        if ($transType == 'credit' && $moduleStatus->mlm_plan == 'Donation') {
            $postData = [
                'action' => 'not_register',
                'user_id' => $to->id,
                'price' => null,
                'sponsor_id' => $to->sponsor->id ?? 0,
            ];
            Http::timeout(60 * 60)->withHeaders([
                'prefix' => session()->get('prefix'),
                'SECRET_KEY' => $this->secret,
            ])
                ->asForm()->post("{$this->URL}calculateDonation", ['enc_data' => encryptData($postData)]);
        }

        return true;
    }

    public function deductUserBalance($user, $amount)
    {
        $currentBalance = $user->userBalance->balance_amount;
        $newBalance = $currentBalance - $amount;
        $user->userBalance()->update(['balance_amount' => $newBalance]);

        return true;
    }

    public function deductPurchaseBalance($user, $amount, $moduleStatus, $userRefId, $transactionId, $type)
    {
        $purchaseWalletHistory = false;
        $currentBalance = $user->userBalance->purchase_wallet;
        $newBalance = $currentBalance - $amount;
        $res = $user->userBalance()->update(['purchase_wallet' => $newBalance]);

        if ($res) {
            $purchaseWalletHistory = $this->addToPurchaseWalletHistory($userRefId, $user, $amount, $amount, $type, 'debit', $transactionId, 'purchase_wallet_payment');
        }

        return $purchaseWalletHistory;
    }

    public function insertUsedEwallet($moduleStatus, $userRefId, $user, $amount, $transactionId, $type)
    {
        $ewalletPaymnet = EwalletPaymentDetail::create([
            'used_user'         => $userRefId['id'],
            'user_id'           => $userRefId['id'],
            'amount'            => $amount,
            'transaction_id'    => $transactionId,
            'used_for'          => $type,
        ]);

        $paymentId = $ewalletPaymnet->id;
        $ewalletHistory = $this->addToEwalletPurchaseHistory($moduleStatus, $user, $paymentId, $type, $amount, $amount, $type, 'ewallet_payment');

        if ($ewalletHistory) {
            return true;
        }
    }

    public function insertUsedPurchaseWallet($moduleStatus, $userRefId, $user, $amount, $transactionId, $type)
    {
        $purchaseWalletPaymnet = EwalletPaymentDetail::create([
            'used_user' => $userRefId['id'],
            'user_id' => $user['id'],
            'amount' => $amount,
            'transaction_id' => $transactionId,
            //'type'                =>      $type,
            'used_for' => $type,
        ]);

        $paymentId = $purchaseWalletPaymnet->id;

        $ewalletHistory = $this->addToEwalletHistory($moduleStatus, $userRefId, $user, $paymentId, $amount, $type, 'debit', $transactionId, 0, null, 'ewallet_payment');

        if ($ewalletHistory) {
            return true;
        }
    }

    public function getTotalEwalletBalanceOfAllUser()
    {
        $userBalanceAmount = UserBalanceAmount::sum('balance_amount');

        return $userBalanceAmount;
    }


    public function addToPurchaseWalletHistory($userRefId, $user, $amount, $purchaseAmount, $amountType, $transType, $transactionId, $walletType, $tds = '0')
    {

        $purchaseWalletHistory = Purchasewallethistory::create([
            'user_id' => $userRefId['id'],
            'from_id' => $user['id'],
            'amount' => $amount,
            'purchase_wallet' => $purchaseAmount,
            'amount_type' => $amountType,
            'transaction_id' => $transactionId,
            'type'  => $transType,
            'tds' => $tds,
        ]);
        return $purchaseWalletHistory;
    }

    public function updateEwalletHistory($referenceId,  $ewalletType)
    {
        $ewalletHistory = EwalletPurchaseHistory::where('ewallet_type', $ewalletType)->where('reference_id', $referenceId)->first();
        $ewalletHistory->amount_type = 'payout_release';
        $ewalletHistory->push();

        return true;
    }

    public function addToEwalletTransferHistory($moduleStatus, $from, $to, $reference, $amount, $amountType, $transType, $transactionId, $fee, $notes)
    {
        $currentBalance = $to->userBalance()->first()->balance_amount ?? 0;
        if ($amountType == 'admin_credit') {
            $balance        = $currentBalance + $amount;
        } elseif ($amountType == 'admin_debit') {
            $fromBalance    = $from->userBalance->balance_amount;
            $balance        = $currentBalance - $amount;
            $from           = null;
        } elseif ($amountType == 'admin_user_credit') {
            $balance        = $currentBalance + $amount;
            $to             = $to;
        } elseif ($amountType == 'admin_user_debit') {
            $balance        = $from->userBalance->balance_amount - ($amount + $fee);
            $to             = $from;
        } elseif ($amountType == 'payout_release') {
            $fromBalance    = 0;
            $balance        = $currentBalance;
            $from           = null;
        } else {
            if ($transType == "credit") {
                $fromBalance    = ($from) ? $from->userBalance->balance_amount - $amount : 0;
                $balance        = $currentBalance + $amount;
            } else {
                $fromBalance    = ($from) ? $from->userBalance->balance_amount - $amount : 0;
                $balance        = $currentBalance - $amount;
            }
            $from           = ($from) ? $from->id : null;
        }

        $ewalletTransferHistory                     = new EwalletTransferHistory;
        $ewalletTransferHistory->user_id            = $to->id ?? $to;
        $ewalletTransferHistory->amount             = $amount;
        $ewalletTransferHistory->balance            = $balance;
        $ewalletTransferHistory->amount_type        = $amountType;
        $ewalletTransferHistory->type               = $transType;
        $ewalletTransferHistory->date_added         = now();
        $ewalletTransferHistory->transaction_id     = $transactionId;
        $ewalletTransferHistory->fund_transfer_id       = $reference;
        $ewalletTransferHistory->transaction_fee    = $fee;
        $ewalletTransferHistory->transaction_note   = $notes;
        $ewalletTransferHistory->save();

        if ($transType == 'credit' && $moduleStatus->mlm_plan == 'Donation') {
            $postData = [
                'action' => 'not_register',
                'user_id' => $to->id,
                'price' => null,
                'sponsor_id' => $to->sponsor->id ?? 0,
            ];
            Http::timeout(60 * 60)->withHeaders([
                'prefix' => session()->get('prefix'),
                'SECRET_KEY' => $this->secret,
            ])
                ->asForm()->post("{$this->URL}calculateDonation", ['enc_data' => encryptData($postData)]);
        }
        if ($amountType == 'admin_user_credit') {
            $this->addToEwalletTransferHistory($moduleStatus, $from, $to, $reference, $amount, 'admin_user_debit', 'debit', $transactionId, $fee, $notes);
        }
        return true;
    }

    public function addToEwalletPurchaseHistory($moduleStatus, $user, $referenceId, $ewalletType, $amount, $balance, $amountType, $type)
    {
        EwalletPurchaseHistory::create([
            'user_id' => $user->id,
            'reference_id' => $referenceId,
            'ewallet_type' => $type,
            'amount' => $amount,
            'balance' => $balance,
            'amount_type' => $amountType,
            'type' => 'debit',
            'date_added' => now()
        ]);

        if ($type == 'credit' && $moduleStatus->mlm_plan == 'Donation') {
            $postData = [
                'action' => 'not_register',
                'user_id' => $user,
                'price' => null,
                'sponsor_id' => $user->sponsor->id ?? 0,
            ];
            Http::timeout(60 * 60)->withHeaders([
                'prefix' => session()->get('prefix'),
                'SECRET_KEY' => $this->secret,
            ])
                ->asForm()->post("{$this->URL}calculateDonation", ['enc_data' => encryptData($postData)]);
        }

        return true;
    }
}
