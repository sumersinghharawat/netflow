<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Package;
use App\Models\UserDetail;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pack = Package::first();

        $user                   = new User();
        $user->date_of_joining  = now();
        $user->user_type        = 'admin';
        $user->product_id       = $pack->id;
        $user->email            = 'email@email.com';
        $user->username   = 'mumin';
$user->password   = Hash::make('admin123');
$user->save();


$user->userBalance()->create([
       'user_id' => $user->id,
                'balance_amount' => 0,
                'purchase_wallet' => 0,
        ]);


        $username    = $user->username;
        $userDetails = new UserDetail;
        $userDetails->user_id = $user->id;
        $userDetails->country_id = 100;
        $userDetails->name = Str::ucfirst($username);
        $userDetails->second_name = '';
        $userDetails->mobile = 000000000000;
        // $userDetails->email = 'email@email.com';
        $userDetails->gender = 'M';
        $userDetails->join_date = now();
        $userDetails->save();
        $user_reg_details = [
            'username' => $username,
            'name' => Str::ucfirst($username),
            'second_name' => '',
            'reg_amount' => 0,
            'product_id' => null,
            'product_pv' => 0,
            'product_amount' => 0,
            'total_amount' => 0,
            'email' => 'email@email.com',
            'country_id' => 100,
        ];
        $user->userRegDetails()->create($user_reg_details);
        $user->transPassword()->create([
            'password' => Hash::make('12345678'),
        ]);
    }
}
