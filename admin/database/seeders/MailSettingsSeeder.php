<?php

namespace Database\Seeders;

use App\Models\Mailsetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mail_settings')->delete();
        Mailsetting::create([
            'from_name' => 'Infinitemlmsoftware.com',
            'from_email' => 'info@infinitemlmsoftware.com',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_username' => 'rd1.ioss1055',
            'smtp_password' => 'tkkyghpxdecsnigd',
            'smtp_port' => 465,
            'smtp_timeout' => 290,
            'reg_mailstatus' => 1,
            'reg_mailcontent' => '<p>test</p>',
            'reg_mailtype' => 'normal',
            'smtp_authentication' => 1,
            'smtp_protocol' => 'tls',
        ]);
    }
}
