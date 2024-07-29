<?php

namespace Database\Seeders;

use App\Models\UsernameConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsernameConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('username_configs')->delete();
        UsernameConfig::create([
            'length' => '6;20',
            'prefix_status' => 1,
            'prefix' => 'INF',
            'user_name_type' => 'static',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
