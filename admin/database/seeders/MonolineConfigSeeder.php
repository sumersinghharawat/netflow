<?php

namespace Database\Seeders;

use App\Models\MonolineConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonolineConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        MonolineConfig::create([
            'downline_count' => 5,
            'bonus' => 25,
            'referral_count' => 15,
        ]);
    }
}
