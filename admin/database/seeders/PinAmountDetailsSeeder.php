<?php

namespace Database\Seeders;

use App\Models\PinAmountDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PinAmountDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pin_amount_details')->delete();
        $data = [
            [
                'amount' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'amount' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'amount' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'amount' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'amount' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'amount' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'amount' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'amount' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        PinAmountDetails::insert($data);
    }
}
