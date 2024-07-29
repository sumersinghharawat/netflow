<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\LegAmount;
use App\Models\ModuleStatus;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegAmountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('leg_amounts')->delete();

        $users = User::all();
        $amounts = collect([1200, 1000, 550, 20, 10, 1.5, 20, 6, 7, 800, 963, 5, 25, 26, 42, 90, 100, 250, 360, 84]);
        $tax = Configuration::select('tds', 'service_charge', 'purchase_income_perc')->first();
        $amountType = collect(['level_commission', 'referral', 'rank_bonus', 'vacation_fund', 'car_fund', 'education_fund', 'house_fund']);
        $package = Package::where('type', 'registration')->get();
        $moduleStatus = ModuleStatus::first()->purchase_wallet;

        for ($i = 0; $i < 2; $i++) {
            foreach ($users as  $user) {
                $user = $users->random()->id;
                $from = $users->random();
                $totalAmount = $amounts->random();
                $tds = ($totalAmount * $tax->tds) / 100;
                $serviceCharge = ($totalAmount * $tax->service_charge) / 100;
                $purchaseWallet = ($moduleStatus) ? ($totalAmount * $tax->purchase_income_perc) / 100 : 0;
                $pack = $package->random();
                LegAmount::create([
                    'user_id' => $user,
                    'from_id' => $from->id,
                    'total_amount' => $totalAmount,
                    'amount_payable' => $totalAmount - $tds - $serviceCharge,
                    'purchase_wallet' => $purchaseWallet,
                    'amount_type' => $amountType->random(),
                    'tds' => $tds,
                    'service_charge' => $serviceCharge,
                    'user_level' => $from->user_level,
                    'product_id' => $pack->id,
                    'pair_value' => $pack->pair_value,
                    'product_value' => $pack->price,
                    // 'created_at'                =>      Carbon::today()->subDays(rand(0, 365)),
                    // 'date_of_submission'        =>      Carbon::today()->subDays(rand(0, 365)),
                    'created_at' => now(),
                    'date_of_submission' => now(),

                ]);
            }
        }
    }
}
