<?php

namespace Database\Seeders;

use App\Models\PinConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PinConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pin_configs')->delete();
        PinConfig::create([
            'amount' => 49,
            'length' => 10,
            'character_set' => 'alphanumeric',
            'max_count' => 100,
        ]);
    }
}
