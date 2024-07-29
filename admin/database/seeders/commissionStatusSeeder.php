<?php

namespace Database\Seeders;

use App\Models\CommissionStatusHistory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class commissionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        config(['database.connections.mysql.prefix' => "14963_"]);
        DB::purge('mysql');
        DB::connection('mysql');
        $admin = User::GetAdmin();
        $status = collect([0, 1, 2, 3]);


        for ($i = 1; $i <= 30; $i++) {
            $history = CommissionStatusHistory::create([
                'commission' => 'referral',
                'user_id' =>    $admin->id,
                'status' => $status->random(),
                'date'  => now(),
                'called_by' => 'admin',
            ]);

            $postData = [
                'user_id' => $admin->id,
                'referral_id' => $admin->id,
                'product_pv' => 50,
                'status_id' => $history->id,
            ];

            $history->update([
                'data' => json_encode($postData),
            ]);
        }
    }
}
