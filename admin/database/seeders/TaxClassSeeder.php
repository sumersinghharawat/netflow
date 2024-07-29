<?php

namespace Database\Seeders;

use App\Models\TaxClass;
use Illuminate\Database\Seeder;

class TaxClassSeeder extends Seeder
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
                'title' => 'Taxable Goods',
                'description' => 'taxable',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Downloadable Products',
                'description' => 'Downloadable',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        TaxClass::insert($data);
    }
}
