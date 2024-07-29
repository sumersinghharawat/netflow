<?php

namespace Database\Seeders;

use App\Models\Placeholders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Placeholderseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('placeholders')->delete();
        $data = [
            [
                'placeholder' => 'first_name',
                'name' => 'name',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placeholder' => 'password',
                'name' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placeholder' => 'username',
                'name' => 'username',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placeholder' => 'admin_user_name',
                'name' => 'adminUserName',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placeholder' => 'payout_amount',
                'name' => 'payoutAmount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placeholder' => 'fullname',
                'name' => 'fullName',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placeholder' => 'company_name',
                'name' => 'companyName',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placeholder' => 'full_name',
                'name' => 'full_Name',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placeholder' => 'new_password',
                'name' => 'newPassword',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];
        Placeholders::insert($data);
    }
}
