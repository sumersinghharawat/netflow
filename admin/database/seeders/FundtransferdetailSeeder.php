<?php

namespace Database\Seeders;

use App\Models\Fundtransferdetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FundtransferdetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fund_transfer_details')->delete();
        $users = User::all();
        $data = [
            [
                'from_id' => $users->random()->id,
                'to_id' => $users->random()->id,
                'amount' => '100',
                'amount_type' => 'admin_credit',
                'notes' => 'test',
                'trans_fee' => '100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'from_id' => $users->random()->id,
                'to_id' => $users->random()->id,
                'amount' => '150',
                'amount_type' => 'user_credit',
                'notes' => 'test',
                'trans_fee' => '100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'from_id' => $users->random()->id,
                'to_id' => $users->random()->id,
                'amount' => '150',
                'amount_type' => 'user_debit',
                'notes' => 'test',
                'trans_fee' => '100',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];
        Fundtransferdetail::insert($data);
    }
}
