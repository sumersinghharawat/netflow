<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentGatewayConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_gateway_configs')->delete();
        $data = [
            // [
            //     'name' => 'Authorize.Net',
            //     'status' => '1',
            //     'logo' => 'Authorizenet_logo.png',
            // ],

            // [
            //     'name' => 'Blockchain',
            //     'status' => '1',
            //     'logo' => 'blockchain.png',
            // ],

            // [
            //     'name' => 'Bitgo',
            //     'status' => '1',
            //     'logo' => 'bitgo.png',
            //     'sort_order' => '3',
            // ],

            // [
            //     'name' => 'Payeer',
            //     'status' => '1',
            //     'logo' => 'payeer_logo.png',
            // ],

            // [
            //     'name' => 'Sofort',
            //     'status' => '1',
            //     'logo' => 'sofort.png',
            // ],

            // [
            //     'name' => 'SquareUp',
            //     'status' => '1',
            //     'logo' => 'squareup.png',
            // ],

            [
                'name' => 'E-pin',
                'status' => '0',
                'logo' => '',
                'payment_only' => 1,
                'gate_way' => 0
            ],

            [
                'name' => 'E-wallet',
                'status' => '1',
                'logo' => '',
                'payment_only' => 1,
                'gate_way' => 0

            ],

            [
                'name' => 'Free Joining',
                'status' => '1',
                'logo' => '',
                'payment_only' => 1,
                'gate_way' => 0

            ],

            [
                'name' => 'Bank Transfer',
                'status' => '1',
                'logo' => '',
                'payment_only' => 0,
                'gate_way' => 0,
                'payout_status' => 1,
            ],
            [
                'name' => 'Stripe',
                'status' => '0',
                'logo' => 'stripe-logo.png',
                'payment_only' => 0,
                'gate_way' => 1,
                'payout_status' => 0,
            ],
            [
                'name' => 'Paypal',
                'status' => '0',
                'logo' => 'paypal.png',
                'payment_only' => 0,
                'gate_way' => 1,
                'payout_status' => 0,
            ],
            [
                'name' => 'Cash On Delivery',
                'status' => '0',
                'logo' => '',
                'payment_only' => 0,
                'gate_way' => 0,
                'slug' => 'cod',
                'repurchase' => 0,
                'membership_renewal' => 0,
                'upgradation' => 0,
            ],

        ];

        foreach ($data as $key => $value) {
            $model = new PaymentGatewayConfig;
            $value['sort_order'] = $key;
            if ($value['name'] != 'Cash On Delivery') {
                $value['slug'] = Str::slug($value['name']);
            }
            $value['mode'] = 'test';
            $value['payout_sort_order'] = $key;
            $value['registration'] = '1';
            if ($value['name'] != 'Cash On Delivery') {
                $value['repurchase'] = '1';
                $value['membership_renewal'] = '1';
                $value['upgradation'] = '1';
            }
            $value['admin_only'] = '0';
            $value['reg_pending_status'] = ($value['slug'] == 'free-joining' || $value['slug'] == 'bank-transfer') ? 1 : 0;
            $model->fill($value);
            $model->save();
        }
    }
}
