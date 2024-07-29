<?php

namespace Database\Seeders;

use App\Models\TransactionPassword;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TransactionPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transaction_passwords')->delete();
        $admin = User::GetAdmin();
        TransactionPassword::create([
            'user_id' => $admin->id,
            'password' => Hash::make('123456'),
        ]);
    }
}
