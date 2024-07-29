<?php

namespace Database\Seeders;

use App\Models\SignupSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SignupSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('signup_settings')->delete();
        SignupSetting::create([
            'registration_allowed' => 1,
            'sponsor_required' => 1,
            'mail_notification' => 1,
            'binary_leg' => 'any',
            'age_limit' => 18,
            'bank_info_required' => 1,
            'compression_commission' => 0,
            'default_country' => 99, //TODO USA
            'email_verification' => 0,
            'login_unapproved' => 0,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
    }
}
