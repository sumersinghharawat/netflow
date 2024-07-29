<?php

namespace Database\Seeders;

use App\Models\ReplicaBanner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReplicaBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('replica_banners')->delete();

        $data = [
            [
                'image' => asset('assets/replica/img/banner/banner-1.jpg'),
                'is_default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => asset('assets/replica/img/banner/banner-2.jpg'),
                'is_default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        ReplicaBanner::insert($data);
    }
}
