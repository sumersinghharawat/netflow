<?php

namespace Database\Seeders;

use App\Models\ModuleStatus;
use App\Models\RankConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RankConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rank_configurations')->delete();
        $data = collect([
            ['name' => 'Referral Count'],
            ['name' => 'Personal PV', 'isProduct_dependent' => 1],
            ['name' => 'Group PV', 'isProduct_dependent' => 1],
            // ['name' => 'Joiner Package', 'isProduct_dependent' => 1],
            ['name' => 'Downline Member Count'],
            // ['name' => 'Downline Package Count', 'isProduct_dependent' => 1],
            ['name' => 'Downline Rank Count'],
        ]);
        $moduleStatus = ModuleStatus::first();
        if ($moduleStatus->product_status || $moduleStatus->ecom_status) {
            $data->push(
                ['name' => 'Joiner Package', 'isProduct_dependent' => 1],
                ['name' => 'Downline Package Count', 'isProduct_dependent' => 1]
            );
        }
        $data->map(function ($da) use ($moduleStatus) {
            $da['slug'] = Str::slug($da['name']);
            if ($da['name'] == 'Joiner Package' && $moduleStatus->product_status) {
                $da['status'] = 1;
            } else if ($da['name'] == 'Joiner Package') {
                $da['status'] = 0;
            }
            RankConfiguration::create($da);
        })->toArray();
    }
}
