<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;

class AdminDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('user_type', 'admin')->first();

        UserDetail::create([
            'user_id' => $user->id,
            'country_id' => 100,
            'name' => 'Admin',
            'second_name' => '',
            'mobile' => 999999999,
            'email' => 'email@email.com',
            'gender' => 'M',
            'join_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user_reg_details = [
            'username' => 'admin',
            'name' => 'Admin',
            'second_name' => '',
            'reg_amount' => 0,
            'product_id' => 1,
            'product_pv' => 0,
            'product_amount' => 0,
            'reg_amount' => 0,
            'total_amount' => 0,
            'email' => 'email@email.com',
            'country_id' => 100,
            'payment_method' => '',
        ];
        $user->userRegDetails()->create($user_reg_details);
    }
}
