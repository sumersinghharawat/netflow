<?php

namespace App\Services;

use App\Models\PaypalProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
class PaypalService
{

    public function payPalBalance()
    {
        $provider = $this->getPaypalCridentials();
        $accessToken = $provider->getAccessToken();
        if (isset($accessToken['access_token'])) {

            $response = Http::timeout(60 * 60)->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken['access_token'],
            ])->get('https://api-m.sandbox.paypal.com/v1/reporting/balances?currency_code=ALL&include_crypto_currencies=true');

            $amount = $response->collect();

            return $amount['balances'][0]['available_balance']['value'] ?? 0;
        }

        return 0;
    }

    public function getPaypalCridentials($prefix = '')
    {
        if ($prefix && config('mlm.demo_status') == 'yes') {
            config(['database.connections.mysql.prefix' => "{$prefix}"]);
            DB::purge('mysql');
            DB::connection('mysql');
        }
        $paypalConfig = getPaypalConfigs();
        $pc           = config('paypal');
        $config = [
            'mode'    => $paypalConfig['mode'] == 'test' ? 'sandbox' : 'live',
            'live' => [
                'client_id'         => $paypalConfig['client_id'],
                'client_secret'     => $paypalConfig['client_secret'],
                'app_id'            => 'APP-80W284485P519543T',
            ],
            'sandbox' => [
                'client_id'         => $paypalConfig['client_id'],
                'client_secret'     => $paypalConfig['client_secret'],
                'app_id'            => 'APP-80W284485P519543T',
            ],

            'payment_action' => 'Sale',
            'currency'       => 'USD',
            'notify_url'     => 'https://your-app.com/paypal/notify',
            'locale'         => 'en_US',
            'validate_ssl'   => true,
        ];
        $config = array_merge($pc, $config);
        Config::set('paypal', $config);
        $provider = new paypalClient();
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);
        return $provider;
    }
    public function createPaypalProduct($data)
    {
        try {
            $provider = new PayPalClient();
            $accessToken = $provider->getAccessToken();
            if (isset($accessToken['access_token'])) {

                $product = Http::timeout(60 * 60)->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken['access_token'],
                ])->post('https://api-m.sandbox.paypal.com/v1/catalogs/products', [
                    'name' => $data['name'],
                ]);


                $plan = Http::timeout(60 * 60)->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken['access_token'],
                ])->post('https://api-m.sandbox.paypal.com/v1/billing/plans', [
                    'product_id' => $product['id'],
                    'name' => $data['name'] . ' Plan',
                    'status' => 'ACTIVE',
                    'billing_cycles' => [
                        [
                            'frequency' => [
                                'interval_unit' => 'MONTH',
                                'interval_count' => $data['validity']
                            ],
                            'tenure_type' => 'TRIAL',
                            'sequence' => 1,
                            'total_cycles' => 1,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value' => $data['price'],
                                    'currency_code' => 'USD'
                                ]
                            ]
                        ],
                        [
                            'frequency' => [
                                'interval_unit' => 'MONTH',
                                'interval_count' => $data['validity']
                            ],
                            'tenure_type' => 'REGULAR',
                            'sequence' => 2,
                            'total_cycles' => 12,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value' => $data['price'],
                                    'currency_code' => 'USD'
                                ]
                            ]
                        ]
                    ],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => false
                    ]
                ]);

                $product_data = new PaypalProducts;
                $product_data->product_id = $data['id'];
                $product_data->paypal_product_id = $product['id'];
                $product_data->plan_id = $plan['id'];
                $product_data->product_data = json_encode($product->collect());
                $product_data->plan_data = json_encode($plan->collect());
                $product_data->amount = $data['price'];
                $product_data->type = 'subscription';
                $product_data->save();

                if ($product_data->id) {
                    return response()->json([
                        'status' => true,
                        'message' => __('package.productId_create_success'),
                        'payment_id' => $plan['id'],
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'product ID creation has been failed.'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Access Token'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
