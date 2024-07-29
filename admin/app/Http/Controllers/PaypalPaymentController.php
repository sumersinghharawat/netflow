<?php

namespace App\Http\Controllers;

use App\Models\PayoutReleaseRequest;
use App\Models\PaypalOrder;
use App\Models\PaypalPayoutBatchDetails;
use App\Models\PaypalPayoutBatchFailureHistory;
use App\Models\UserBalanceAmount;
use App\Services\EwalletService;
use Illuminate\Http\Request;
use App\Services\PaypalService;
use Illuminate\Support\Facades\DB;

class PaypalPaymentController extends CoreInfController
{
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['type_renew'])) {
            $description = 'Member Renewal';
            $type = 'Renewal';
        } elseif (isset($data['type_upgrade'])) {
            $description = 'Package Upgrade';
            $type = 'Upgrade';
        } elseif (isset($data['type_cart'])) {
            $description = 'Internal Cart';
            $type = 'Cart';
        } else {
            $description = 'Member registration';
            $type = 'Registration';
        }
        $totalAmount = $data['amount'];

        $paypalCurrencyCode = "USD";
        $paypalCurrencyLeftSymbol = "$";
        $paypalCurrencyRightSymbol = "";
        $description .= "\nMembership Fee : $paypalCurrencyLeftSymbol $totalAmount $paypalCurrencyRightSymbol";
        $service = new PaypalService;
        $provider = $service->getPaypalCridentials($data['prefix']);

        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $paypalCurrencyCode,
                        "value" => $totalAmount
                    ],
                    'description' => $description
                ]
            ],
        ]);

        $Orders = new PaypalOrder();
        $Orders->user_id = $data['user_id'];
        $Orders->amount = $data['amount'];
        $Orders->order_id = $order['id'];
        $Orders->type = $type;
        $Orders->currency = $paypalCurrencyCode;
        $Orders->package_id = $data['package_id'];
        $Orders->status = 0;
        $Orders->save();

        return response()->json($order);
    }

    public function capture(Request $request)
    {
        $data       = json_decode($request->getContent(), true);
        $orderId    = $data['orderId'];
        $service    = new PaypalService;
        $provider   = $service->getPaypalCridentials($data['prefix']);
        $result     = $provider->capturePaymentOrder($orderId);

        try {
            if (isset($result['status']) && $result['status'] === "COMPLETED") {
                $orders = PaypalOrder::where('order_id', $orderId)->first();
                $orders->status = 1;
                $orders->push();
            }
        } catch (\Throwable $e) {
            $orders = PaypalOrder::where('order_id', $orderId)->first();
            $orders->status = 2;
            $orders->push();
            return response()->json($e->getMessage(), 404);
            dd($e);
        }
        return response()->json($result);
    }


    public function webhooksPayoutSuccess(Request $request)
    {
        try {
            $demoStatus = config('mlm.demo_status');
            $requests = json_encode($request->all());
            $data = json_decode($requests, true);
            $batchId = $data['resource']['payout_batch_id'];
            $prefix  = $data['resource']['payout_item']['note'];
            $transactionStatus = $data['resource']['transaction_status'];
            $prefix = str_replace('prefix_', '', $prefix);

            if ($demoStatus == 'yes') {
                config(['database.connections.mysql.prefix' => "{$prefix}_"]);
                DB::purge('mysql');
                DB::connection('mysql');
            }
            $batchDetails = PaypalPayoutBatchDetails::where('batch_id', $batchId)->where('status', false)->first();
            $batchDetails->status = 1;
            $batchDetails->webhook_data = $data;
            $batchDetails->reference->status = 1;
            $batchDetails->batch_status = $transactionStatus;
            $batchDetails->push();

            return true;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function webhooksPayoutFail(Request $request)
    {
        try {
            $demoStatus = config('mlm.demo_status');
            $requests = json_encode($request->all());
            $data = json_decode($requests, true);
            $batchId = $data['resource']['batch_header']['payout_batch_id'];
            $prefix  = $data['resource']['batch_header']['payout_item']['note'];

            $transactionStatus = $data['resource']['batch_header']['batch_status'];
            $prefix = str_replace('prefix_', '', $prefix);

            if ($demoStatus == 'yes') {
                config(['database.connections.mysql.prefix' => "{$prefix}_"]);
                DB::purge('mysql');
                DB::connection('mysql');
            }
            DB::beginTransaction();
            $batchDetails = PaypalPayoutBatchDetails::where('batch_id', $batchId)->where('status', false)->first();
            $batchDetails->status = 0;
            $batchDetails->webhook_data = $data;
            $batchDetails->batch_status = $transactionStatus;
            $batchDetails->push();

            $payoutFee = $batchDetails->reference->payout_fee;
            $amount    = $batchDetails->reference->amount;
            $moduleStatus = $this->moduleStatus();
            $refernceId = $batchDetails->reference_id;
            $refundAmonut = round($amount + $payoutFee, 8);
            $ewalletService =  new EwalletService;

            $ewalletService->addToEwalletHistory($moduleStatus, null, $batchDetails->reference->user, $refernceId, $refundAmonut, 'payout_delete', 'credit', null, $payoutFee, null, 'payout');

            $userBalance = UserBalanceAmount::where('user_id', $batchDetails->user_id);
            $refundTotal = $userBalance->balance_amount + $refundAmonut;
            $userBalance->balance_amount = $refundTotal;
            $userBalance->push();

            if ($batchDetails->reference->request_id != null) {
                $payoutRequest = PayoutReleaseRequest::whereKey($batchDetails->reference->request_id)->first();
                $payoutRequest->status = 2; //  request canceled
                $payoutRequest->push();
            }

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $history = new PaypalPayoutBatchFailureHistory();
            $history->sender_batch_id = $batchId;
            $history->paypal_data = json_encode($batchDetails) ?? [];
            $history->webhook_data = json_encode($data) ?? [];
            $history->exception_message = $th->getMessage() ?? null;
            $history->save();
            return false;
        }
    }
}
