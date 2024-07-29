<?php

namespace Database\Seeders;

use App\Models\ModuleStatus;
use App\Models\ToolTipConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TooltipConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tooltips_config')->delete();
        $moduleStatus = ModuleStatus::first();
        $data = [
            [
                'name' => 'First name',
                'status' => 1,
                'slug' => Str::slug('First name'),
                'view_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Join date',
                'status' => 0,
                'slug' => 'join-date',
                'view_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Personal PV',
                'status' => 1,
                'slug' => Str::slug('Personal PV'),
                'view_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Group PV',
                'status' => 1,
                'slug' => Str::slug('Group PV'),
                'view_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];
        if ($moduleStatus->mlm_plan == 'Binary') {
            array_push(
                $data,
                [
                    'name' => 'Left',
                    'status' => 1,
                    'slug' => 'left',
                    'view_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Right',
                    'status' => 1,
                    'slug' => 'right',
                    'view_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Left carry',
                    'status' => 1,
                    'slug' => 'left-carry',
                    'view_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Right carry',
                    'status' => 1,
                    'slug' => 'right-carry',
                    'view_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        } elseif ($moduleStatus->mlm_plan == 'Donation') {
            array_push(
                $data,
                [
                    'name' => 'Donation level',
                    'status' => 1,
                    'slug' => 'donation-level',
                    'view_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

            );
        }

        if ($moduleStatus->rank_status) {
            array_push(
                $data,
                [
                    'name' => 'Rank status',
                    'status' => 1,
                    'slug' => Str::slug('Rank status'),
                    'view_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        ToolTipConfig::insert($data);
    }
}
