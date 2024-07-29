<?php

namespace Database\Seeders;

use App\Models\AmountType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('amount_types')->truncate();
        $data = [
            [
                'type' => 'pin_purchased',
                'view_type' => 'Pin purchased',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'payout_released',
                'view_type' => 'Payout released',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'referral',
                'view_type' => 'Referral commission',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],

            [
                'type' => 'leg',
                'view_type' => 'Binary Commission',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'rank_bonus',
                'view_type' => 'Rank Commission',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'level_commission',
                'view_type' => 'Level commission',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'repurchase_level_commission',
                'view_type' => 'Level commission by purchase',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'repurchase_leg',
                'view_type' => 'Binary commission by purchase',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type' => 'stair_step',
                'view_type' => 'Stair step',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'type' => 'override_bonus',
                'view_type' => 'Override bonus',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'daily_investment',
                'view_type' => 'Daily investment',
                'status' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'xup_commission',
                'view_type' => 'X-UP Commission',
                'status' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'xup_repurchase_level_commission',
                'view_type' => 'X-UP Commission by Purchase',
                'status' => '0',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'xup_upgrade_level_commission',
                'view_type' => 'X-UP Commission by Upgrade',
                'status' => '0',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'matching_bonus',
                'view_type' => 'Matching Bonus',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'matching_bonus_purchase',
                'view_type' => 'Matching Bonus by Purchase',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'matching_bonus_upgrade',
                'view_type' => 'Matching Bonus by Upgrade',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'pool_bonus',
                'view_type' => 'Pool bonus',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'fast_start_bonus',
                'view_type' => 'Fast Start Bonus',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'vacation_fund',
                'view_type' => 'Vacation fund',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'education_fund',
                'view_type' => 'Education fund',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'car_fund',
                'view_type' => 'Car fund',
                'status' => '0',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'house_fund',
                'view_type' => 'House fund',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'type' => 'sales_commission',
                'view_type' => 'Sales commission',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),

            ],

        ];
        AmountType::insert($data);
    }
}
