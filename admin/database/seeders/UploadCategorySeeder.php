<?php

namespace Database\Seeders;

use App\Models\UploadCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UploadCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('upload_categories')->delete();

        $data = [
            [
                'type' => 'Documents',
            ],
            [
                'type' => 'Images',
            ],
            [
                'type' => 'Videos',
            ],
        ];
        UploadCategory::insert($data);
    }
}
