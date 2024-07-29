<?php

namespace Database\Seeders;

use App\Models\RankConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PresetRankConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rank_configurations')->delete();
        $data = collect([
            ['name' => 'Referral Count'],
            ['name' => 'Personal PV', 'isProduct_dependent' => 1],
            ['name' => 'Group PV', 'isProduct_dependent' => 1],
            ['name' => 'Joiner Package', 'isProduct_dependent' => 1],
            ['name' => 'Downline Member Count'],
            ['name' => 'Downline Package Count', 'isProduct_dependent' => 1],
            ['name' => 'Downline Rank Count'],
        ]);
        $data->map(function ($da) {
            $da['slug'] = Str::slug($da['name']);
            if ($da['name'] == 'Joiner Package') {
                $da['status'] = 1;
            }
            RankConfiguration::create($da);
        })->toArray();
    }
}
