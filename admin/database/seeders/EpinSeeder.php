<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EpinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PinConfigSeeder::class,
            PinAmountDetailsSeeder::class,
            PaymentGatewayConfigSeeder::class,
            PendingSignupConfigSeeder::class,
        ]);
    }
}
