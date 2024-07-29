<?php

namespace Database\Seeders;

use App\Models\EwalletHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EwalletHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ewallet_histories')->delete();
        $users = User::all();
        $creditData = collect(['admin_credit', 'leg', 'level_commission', 'referral']);
        $debitData = collect(['admin_debit', 'payout_release_manual']);
        $amount = collect([1.5, 500, 30, 2.8, 1500]);

        for ($i = 0; $i <= 10; $i++) {
            foreach ($users->shuffle() as $user) {
                $userId = $users->random()->id;
                $fromId = ($userId < $user->id) ? $user->id : $users->last()->id;
                $amountType = $creditData->random();
                if ($amountType == 'leg' || $amountType == 'level_commission' || $amountType == 'referral') {
                    $ewalletType = 'commission';
                } else {
                    $ewalletType = 'fund_transfer';
                }
                $walletAmount = $amount->random();
                $balance = EwalletHistory::where('user_id', $userId)->select('balance')->orderBy('id', 'desc')->first()->balance ?? 0;
                EwalletHistory::create([
                    'user_id' => $userId,
                    'from_id' => $fromId,
                    'reference_id' => 1,
                    'ewallet_type' => $ewalletType,
                    'amount' => $walletAmount,
                    'purchase_wallet' => 0,
                    'balance' => $balance + $walletAmount,
                    'amount_type' => $amountType,
                    'type' => 'credit',
                    'date_added' => Carbon::today()->subDays(rand(0, 365)),
                    'transaction_fee' => collect([0, 10, 0, 25, 0])->random(),
                    'created_at' => now(),
                ]);
            }
        }

        foreach ($users->take(10)->shuffle() as $user) {
            if ($user->id == 1) {
                continue;
            }
            $amountType = $debitData->random();
            if ($amountType == 'admin_debit') {
                $ewalletType = 'fund_transfer';
            } else {
                $ewalletType = 'payout';
            }
            EwalletHistory::create([
                'user_id' => $user->id,
                'from_id' => null,
                'ewallet_type' => $ewalletType,
                'amount' => $amount[0],
                'purchase_wallet' => 0,
                'amount_type' => $amountType,
                'type' => 'debit',
                'date_added' => Carbon::today()->subDays(rand(0, 365)),
                'transaction_fee' => collect([0, 10, 0, 25, 0])->random(),
                'created_at' => now(),
            ]);
        }
    }
}
