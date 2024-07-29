<?php

namespace Database\Seeders;

use App\Models\PayoutConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayoutConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payout_configurations')->delete();

        PayoutConfiguration::create([
            'release_type' => 'ewallet_request',
            'min_payout' => 10,
            'request_validity' => 30,
            'max_payout' => 500,
            'fee_amount' => 0.07,
            'fee_mode' => 'percentage',

        ]);
    }
}
