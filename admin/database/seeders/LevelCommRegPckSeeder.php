<?php

namespace Database\Seeders;

use App\Models\LevelCommissionRegisterPack;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelCommRegPckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('level_commission_register_packs')->delete();
        $packs = Package::get();
        $data = [];
        $key = 0;
        for ($i = 1; $i < 4; $i++) {
            foreach ($packs as $k => $pack) {
                $data[$key]['package_id'] = $pack->id;
                $data[$key]['level'] = $i;
                $data[$key]['commission'] = 3;
                $data[$key]['created_at'] = now();
                $data[$key]['updated_at'] = now();
                $key++;
            }
        }

        LevelCommissionRegisterPack::insert($data);
    }
}
