<?php

namespace Database\Seeders;

use App\Models\MatchingLevelCommission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatchingLevelCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('matching_level_commissions')->delete();
        $data = [
            [
                'level_no' => 1,
                'level_percentage' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_no' => 2,
                'level_percentage' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_no' => 3,
                'level_percentage' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];
        MatchingLevelCommission::insert($data);
    }
}
