<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CurrencyDetailsSeeder::class,
            PerformanceBonusSeeder::class,
            FastStartBonusSeeder::class,
            ReplicaSeeder::class,
            TicketStatusSeeder::class,
            TicketPrioritySeeder::class,
            SubscriptionConfigSeeder::class,
            EmployeeDashboardItemSeeder::class,
        ]);
    }
}
