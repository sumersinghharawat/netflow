<?php

namespace Database\Seeders;

use App\Models\WeightClass;
use Illuminate\Database\Seeder;

class WeightClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'value' => 1.000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value' => 10.000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value' => 15.000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value' => 20.000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        WeightClass::insert($data);
    }
}
