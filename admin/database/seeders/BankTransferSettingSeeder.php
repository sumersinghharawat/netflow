<?php

namespace Database\Seeders;

use App\Models\BankTransferSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankTransferSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bank_transfer_settings')->delete();
        BankTransferSettings::create([
            'account_info' => 'NFTHSGBCVGHDJDNC',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
