<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rank_details')->delete();
        $ranks = Rank::all();

        $data = [
            [
                'personal_pv' => 50,
                'group_pv' => 100,
                'downline_count' => 10,
                'referral_count' => 4,

            ],
            [
                'personal_pv' => 100,
                'group_pv' => 200,
                'downline_count' => 15,
                'referral_count' => 8,

            ],
            [
                'personal_pv' => 150,
                'group_pv' => 300,
                'downline_count' => 30,
                'referral_count' => 12,

            ],
            [
                'personal_pv' => 250,
                'group_pv' => 600,
                'downline_count' => 50,
                'referral_count' => 25,

            ],

        ];

        $ranks->map(fn ($rank, $key) => $rank->rankDetails()->create($data[$key]));
    }
}
