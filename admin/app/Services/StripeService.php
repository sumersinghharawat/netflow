<?php

namespace App\Services;

use App\Models\AmountPaid;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentGatewayDetail;
use App\Models\StripePaymentDetails;
use App\Models\StripePayoutDetail;
use App\Models\StripePayoutFail;
use App\Models\StripeProducts;
use Throwable;

class StripeService
{
    public function stripePostData($cardnumber, $cardnameInput, $expirydateInput, $cvvcodeInput, $totalAmount)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $token = $stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 6,
                'exp_year' => 2023,
                'cvc' => '314',
            ],
        ]);
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => 500,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);
        $output = [
            'clientSecret' => $paymentIntent->client_secret,
        ];

        echo json_encode($output);
    }

    public function payment($regData, $user)
    {
        $stripe = new \Stripe\StripeClient($this->getStripeCredentials()->secret_key);
        if (isset($regData['type_renew'])) {
            $description = 'Member Renewal';
            $type = 'Renewal';
        } elseif (isset($regData['type_cart'])) {
            $description = 'Internal Cart';
            $type = 'Cart';
        }elseif(isset($regData['type_upgrade'])){
            $description = 'Member Package Upgrade';
            $type = 'upgrade';
        } else {
            $description = 'Member registration';
            $type = 'Registration';
        }
        $response = $stripe->charges->create([
            'amount' => (int) $regData['totalAmount'] * 100,
            'currency' => 'usd',
            'source' => $regData['stripeToken'],
            'description' => $description,
            'metadata' => ['product_id' => $regData['product_id'] ?? $regData['package_id']],
        ]);

        $stripeData = new StripePaymentDetails();
        $stripeData->user_id = $user->id;
        $stripeData->charge_id = $response->id;
        $stripeData->product_id = $regData['product_id'] ?? $regData['package_id'];
        $stripeData->total_amount = $regData['totalAmount'];
        $stripeData->type = $type;
        $stripeData->payment_method = $response->payment_method;
        $stripeData->stripe_response = json_encode($response);
        $stripeData->save();

        return true;
    }

    public function getStripeCredentials()
    {
        $paymentGatewayDetail = PaymentGatewayDetail::whereHas('gateway', fn ($gateway) => $gateway->where('slug', 'stripe'))->first();
        return $paymentGatewayDetail;
    }

    public function createAccount($type, $country, $email)
    {
        try {
            $stripeConfig = $this->getStripeCredentials();
            $stripe = new \Stripe\StripeClient(
                $stripeConfig->secret_key
            );

            $res = $stripe->accounts->create([
                'type' => $type,
                'country' => $country,
                'email' => $email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);

            return $res->id ?? false;
        } catch (\Throwable $th) {
            dd($th);
        }
    }


    public function generateAccountLinks($account, $refreshUrl, $returnUrl)
    {
        try {
            $stripeConfig = $this->getStripeCredentials();

            $stripe = new \Stripe\StripeClient(
                $stripeConfig->secret_key
            );

            $res = $stripe->accountLinks->create([
                'account' => $account->id,
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
            ]);

            return $res->url ?? false;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getAccountRetriveDetails($account)
    {
        try {
            $stripeConfig = $this->getStripeCredentials();

            $stripe = new \Stripe\StripeClient(
                $stripeConfig->secret_key
            );

            $res = $stripe->accounts->retrieve(
                $account,
                []
            );
            return $res;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getBalance()
    {
        try {
            $config = $this->getStripeCredentials();

            $stripe = new \Stripe\StripeClient(
                $config->secret_key
            );

            $res = $stripe->balance->retrieve([]);

            return $res->toArray()['available'][0]['amount'] ?? 0;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function stripePayout($moduleStatus, $user, $payoutData, $account, $request_id = null)
    {
        try {
            $config = $this->getStripeCredentials();
            $ewalletService = new EwalletService;
            $amount = $payoutData['released_amount'];

            $stripe = new \Stripe\StripeClient(
                $config->secret_key
            );
            $res = $stripe->transfers->create([
                'amount' => $payoutData['released_amount'] * 100, // amount should be converted by 100 other wise its will be automatically converted into 100
                'currency' => 'usd', // by default its usd
                'destination' => $account,
            ]);

            $response = $res->toArray();

            $history = new StripePayoutDetail();
            $history->account_id = $account;
            $history->user_id = $payoutData['user_id'];
            $history->response_data = json_encode($response);
            $history->payout_data = json_encode($payoutData);
            $history->status = 1;
            $history->transaction_id = $res->id;

            $amountPaid = AmountPaid::create([
                'user_id' => $payoutData['user_id'],
                'amount' => $amount,
                'date' => now(),
                'transaction_id' => 0,
                'type' => 'released',
                'status' => 1,
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
        } catch (\Throwable $th) {

            $data = new StripePayoutFail();
            $data->user_id = $payoutData['user_id'];
            $data->response_data = json_encode($response);
            $data->payout_data = json_encode($payoutData);
            $data->destination_account = $account;
            $data->exception_message = $th->getMessage();
            $data->save();
            return false;
        }
    }
    public function createStripeProduct($data)
    {
        try {
            $stripe = new \Stripe\StripeClient($this->getStripeCredentials()->secret_key);
            $product = $stripe->products->create([
                'name' => $data['name'],
            ]);
            $price = $stripe->prices->create([
                'unit_amount' => $data['price'] * 100,
                'currency' => 'usd',
                'recurring' => ['interval' => 'month', 'interval_count' => $data['validity']],
                'product' => $product->id,
            ]);

            $product_data = new StripeProducts;
            $product_data->product_id = $data['id'];
            $product_data->stripe_product_id = $product->id;
            $product_data->price_id = $price->id;
            $product_data->product_data = json_encode($product);
            $product_data->price_data = json_encode($price);
            $product_data->amount = $data['price'];
            $product_data->type = 'subscription';
            $product_data->save();

            if ($product_data->id) {
                return response()->json([
                    'status' => true,
                    'message' => __('package.productId_create_success'),
                    'payment_id' => $price->id,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'product ID creation has been failed.'
                ]);
            }
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
