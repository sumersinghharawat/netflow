<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserBalanceAmount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserBalanceAmountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_balance_amounts')->delete();
        $users = User::GetUsers();
        foreach ($users as  $user) {
            $random = collect([100, 210.5, 220, 200, 1500, 500, 3500, 6000]);
            UserBalanceAmount::create([
                'user_id' => $user->id,
                'balance_amount' => $random->random(),
                'purchase_wallet' => $random->random(),
            ]);
        }
    }
}
