<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('infinite_mlm_lead_configs')->delete();

        $data = [
            'email_reuse_count' => 0,
            'phone_reuse_count' => 0,
            'otp_timeout' => 10,
            'indian_preset_demo_timeout' => 24,
            'other_preset_demo_timeout' => 24,
            'indian_custom_demo_timeout' => 3,
            'other_custom_demo_timeout' => 24,
            'custom_demo_delete_timeout'=> 30,
            'unlimited_emails' => 'rd1@teamioss.com, support@ioss.in,bdm@ioss.in,info@ioss.in',
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('infinite_mlm_lead_configs')->insert($data);
    }
}
