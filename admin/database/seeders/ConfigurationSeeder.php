<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\ModuleStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configurations')->delete();
        $data = [
'tds' => 0,
'pair_price' => 300,
'pair_ceiling' => 2000,
'pair_ceiling_type' => 'daily',
'service_charge' => 0,
'product_point_value' => 100,
'pair_value' => 100,
'start_date' => 'Sunday',
'end_date' => 'Saturday',
'sms_status' => 0,
'reg_amount' => 0,
'referral_amount' => 10,
'max_pin_count' => 500,
'pair_commission_type' => 'flat',
'depth_ceiling' => 3,
'width_ceiling' => 2,
'level_commission_type' => 'flat',
'trans_fee' => 0,
'override_commission' => 0,
'profile_updation_history' => 0,
'xup_level' => 1,
'upload_config' => 10000,
'pair_ceiling_monthly' => 0,
'pool_bonus_percent' => 5,
'sponsor_commission_type' => 'sponsor_package',
'purchase_income_perc' => 10,
'commission_criteria' => 'genealogy',
'referral_commission_type' => 'flat',
'commission_upto_level' => 10,
'roi_period' => 'daily',
'roi_days_skip' => '',
'roi_criteria' => 'member_pck',
'skip_blocked_users_commission' => 1,
'pool_bonus_period' => 'yearly',
'pool_bonus_criteria' => 'sales',
'pool_distribution_criteria' => 'equally',
'matching_criteria' => 'genealogy',
'matching_upto_level' => 3,
'sales_criteria' => 'cv',
'sales_type' => 'genealogy',
'sales_level' => 30,
'api_key' => '',
'tree_icon_based' => 'profile_image',
'active_tree_icon' => 'active.jpg',
'inactive_tree_icon' => 'inactive.png',
'default_package_tree_icon' => 'default_package.png',
'default_rank_tree_icon' => 'default_rank.png',
];

        Configuration::insert($data);
    }
}
