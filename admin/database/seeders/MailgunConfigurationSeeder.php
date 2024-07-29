<?php

namespace Database\Seeders;

use App\Models\MailGunConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailgunConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mail_gun_configurations')->delete();
        MailGunConfiguration::create([
            'fromName' => 'ioss',
            'fromEmail' => 'info@infinitemlmsoftware.com',
            'replyTo' => 'Infinitemlmsoftware.com',
            'domain' => 'ioss',
            'apiKey' => 'W71336414asdfdaf',

        ]);
    }
}
