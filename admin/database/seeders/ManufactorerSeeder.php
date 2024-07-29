<?php

namespace Database\Seeders;

use App\Models\Manufactor;
use Illuminate\Database\Seeder;

class ManufactorerSeeder extends Seeder
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
                'name' => 'first',
                'image' => 'no-image.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'second',
                'image' => 'no-image.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'third',
                'image' => 'no-image.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Manufactor::insert($data);
    }
}
