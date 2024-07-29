<?php

namespace Database\Seeders;

use App\Models\KycCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KycCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('kyc_categories')->delete();
        $data = [
            'category' => 'AADHAR',
            'status' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        KycCategory::insert($data);
    }
}
