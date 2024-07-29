<?php

namespace Database\Seeders;

use App\Models\Compensation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompensationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('compensation')->delete();
        $data = [
'plan_commission' => 0,
'sponsor_commission' => 1,
'referral_commission' => 1,
'rank_commission' => 0,
'roi_commission' => 0,
'matching_bonus' => 0,
'pool_bonus' => 0,
'fast_start_bonus' => 0,
'performance_bonus' => 0,
'sales_Commission' => 0,
'created_at' => now(),
'updated_at' => now(),
];

        Compensation::insert($data);
    }
}
