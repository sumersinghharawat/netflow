<?php

namespace Database\Seeders;

use App\Models\EwalletHistory;
use App\Models\Purchasewallethistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseWalletHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purchase_wallet_histories')->delete();
        $users = User::all();
        $ewallet = EwalletHistory::all();

        foreach ($users as $user) {
            $amount = collect([2000, 10, 250, 25, 11, 10, 8, 7, 1500, 800, 35, 689])->random();
            $amountType = collect(['level_commission', 'referral', 'leg', 'rank_bonus', 'vacation_fund', 'matching_bonus']);
            $balance = Purchasewallethistory::where('user_id', 1)->orderBy('id', 'DESC')->first();
            $previous = ($balance) ? $balance->balance : 0;
            Purchasewallethistory::create([
                'user_id' => 1,
                'from_user_id' => $user->id,
                'ewallet_refid' => $ewallet->random()->id,
                'transaction_id' => 0,
                'amount' => $amount,
                'purchase_wallet' => ($amount * 20) / 100,
                'balance' => $previous + ($amount * 20) / 100,
                'amount_type' => $amountType->random(),
                'type' => 'credit',
                'tds' => 0,
                'date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
