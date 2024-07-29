<?php

namespace Database\Seeders;

use App\Models\GenelogyLevelCommission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('genelogy_level_commissions')->delete();
        $data = [
            [
                'level' => 1,
                'percentage' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 2,
                'percentage' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 3,
                'percentage' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 4,
                'percentage' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 5,
                'percentage' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 6,
                'percentage' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 7,
                'percentage' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 8,
                'percentage' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 9,
                'percentage' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 10,
                'percentage' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        GenelogyLevelCommission::insert($data);
    }
}
