<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SupportUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('support_users')->delete();
        DB::table('support_users')->upsert(
            [
                [
                    "name" => "IOSS",
                    "email" => "support@ioss.in",
                    "password" => Hash::make("support@1055"),
                ],
                [
                    "name" => "Hamdi Marwan",
                    "email" => "hamdimarvan0@gmail.com",
                    "password" => "",
                ],
                [
                    "name" => "Sabareesh C S",
                    "email" => "sabareesh@infinitemlm.com",
                    "password" => "",
                ],
                [
                    "name" => "Sojan Thomas",
                    "email" => "sojan@teamioss.in",
                    "password" => "",
                ],
                [
                    "name" => "Jibin Daniel",
                    "email" => "jibin@teamioss.in",
                    "password" => "",
                ],
                [
                    "name" => "Jithin Jyodish",
                    "email" => "jithinjkclt@gmail.com",
                    "password" => "",
                ],
                [
                    "name" => "Sreedev N",
                    "email" => "sreedev@teamioss.in",
                    "password" => "",
                ]
            ],
            ["name"]/*check for duplication*/,
            ["email", "password"]/*update if duplication*/
        );
    }
}
