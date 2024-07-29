<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankConfigDownlineRankCountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rank_downline_rank')->delete();

        $ranks = Rank::all();

        $ranks->map(fn ($rank) => $rank->downlineRankCount()->attach($rank->id, ['count' => 1]));
    }
}
