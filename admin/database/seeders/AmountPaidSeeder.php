<?php

namespace Database\Seeders;

use App\Models\AmountPaid;
use App\Models\User;
use Illuminate\Database\Seeder;

class AmountPaidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::GetUsers();
        $amounts = collect([120, 110, 55, 20, 10, 15, 20, 6, 7, 80, 96, 5, 25, 26, 42, 90, 10, 25, 36, 84]);

        for ($i = 1; $i <= 3; $i++) {
            foreach ($users as $key => $user) {
                $user = $users->random();
                $amount = $amounts->random();
                AmountPaid::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'date' => now(),
                    'type' => 'released',
                    'payout_fee' => 0,
                    'status' => '1',
                    'payment_method' => 'Bank Transfer',
                ]);
            }
        }
    }
}
