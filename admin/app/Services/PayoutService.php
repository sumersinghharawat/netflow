<?php

namespace App\Services;

use App\Models\AmountPaid;
use App\Models\PaypalPayoutBatchDetails;
use App\Models\PaypalPayoutBatchFailureHistory;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PayoutService
{
    public function paypalPayout($user, $email, $senderBatchId, $amount, $payoutData, $prefix, $moduleStatus, $request_id = null)
    {
        try {
            $amount = number_format($amount, 2);
            $provider = new PayPalClient();
            $ewalletService = new EwalletService;
            $provider->getAccessToken();
            $data = json_decode('{
                "sender_batch_header": {
                  "sender_batch_id": "' . $senderBatchId . '",
                  "email_subject": "You have a payout!",
                  "email_message": "You have received a payout! Thanks for using our service!"
                },
                "items": [
                  {
                    "recipient_type": "EMAIL",
                    "amount": {
                      "value": "' . $amount . '",
                      "currency": "USD"
                    },
                    "note": "prefix_' . $prefix . '",
                    "sender_item_id": "201403140001",
                    "receiver": "' . $email . '"
                  }
                ]
              }', true);


            $response = $provider->createBatchPayout($data);

            if (isset($response['batch_header']) && $response['batch_header']['batch_status'] == 'PENDING') {
                $history = new PaypalPayoutBatchDetails();
                $history->batch_id = $response['batch_header']['payout_batch_id'];
                $history->user_id = $payoutData['user_id'];
                $history->response_data = json_encode($response);
                $history->payout_data = json_encode($payoutData);
                $history->paypal_data = json_encode($data);
                $history->batch_status = $response['batch_header']['batch_status'];
                $history->status = 0;

                $amountPaid = AmountPaid::create([
                    'user_id' => $payoutData['user_id'],
                    'amount' => $amount,
                    'date' => now(),
                    'transaction_id' => 0,
                    'type' => 'released',
                    'status' => 0,
                    'payment_method' => $payoutData['payment_method'],
                    'payout_fee' => $payoutData['payout_fee'],
                    'request_id' => $request_id,
                ]);
                $refernceId = $amountPaid->id ?? ' ';

                $history->reference_id = $refernceId;
                $history->save();

                // $addToEwalletHistory = $ewalletService->addToEwalletHistory($moduleStatus, null, $user, $refernceId, $amount, 'payout_release_manual', 'debit', null, $payoutData['payout_fee'], null, 'payout');
                $updateEwalletHisory = $ewalletService->updateEwalletHistory($request_id, 'payout');

                return true;
            }
            return false;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            $history = new PaypalPayoutBatchFailureHistory();
            $history->sender_batch_id = $senderBatchId;
            $history->paypal_data = json_encode($data) ?? [];
            $history->webhook_data = json_encode($response) ?? [];
            $history->exception_message = $th->getMessage() ?? null;
            $history->save();
            return false;
        }
    }
}
