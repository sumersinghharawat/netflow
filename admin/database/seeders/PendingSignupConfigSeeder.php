<?php

namespace Database\Seeders;

use App\Models\PendingSignupConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendingSignupConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pending_signup_configs')->delete();
        $data = [
            [
                'payment_method' => 'E-wallet',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'E-pin',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'Free Joining',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'Paypal',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'Creditcard',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'EPDQ',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'Authorize.Net',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'Bitcoin',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => ' Bank Transfer',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'Blockchain',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'BitGo',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'Payeer',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'Sofort',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'SquareUp',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];
        PendingSignupConfig::insert($data);
        // DB::table('pending_signup_configs')->insert($data);
    }
}
