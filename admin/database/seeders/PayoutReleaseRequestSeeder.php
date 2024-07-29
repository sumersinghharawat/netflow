<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use App\Models\PayoutReleaseRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayoutReleaseRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // config(['database.connections.mysql.prefix' => "15000_"]);
        // DB::purge('mysql');
        // DB::connection('mysql');
        // DB::table('payout_release_requests')->delete();
        $paymentMethod = PaymentGatewayConfig::where('slug', 'bank-transfer')->first();
        $users = User::GetUsers();
        $amounts = collect([120, 110, 55, 20, 10, 1.5, 20, 6, 7, 80, 96, 5, 25, 26, 42, 90, 10, 25, 36, 84]);
        for ($i = 1; $i <= 3; $i++) {
            foreach ($users as $key => $user) {
                $user = $users->random()->id;
                $amount = $amounts->random();
                PayoutReleaseRequest::create([
                    'user_id' => $user,
                    'balance_amount' => $amount,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod->id,
                ]);
            }
        }
    }
}
