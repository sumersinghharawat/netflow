<?php

namespace Database\Seeders;

use App\Models\CommonSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommonSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('common_settings')->delete();
        //
        CommonSetting::create([
            'logout_time' => 600000,
            'active' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
