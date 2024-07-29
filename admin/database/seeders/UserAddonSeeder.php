<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserAddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('addon_commissions')->truncate();

        $data = [
];
        $da = [];
        if(isset($data)){
            foreach ($data as $key => $value) {
                $da[$key]['name']       = $value;
                $da[$key]['slug']       = Str::slug($value);
                $da[$key]['status']     = 1;
                $da[$key]['created_at'] = now();
                $da[$key]['updated_at'] = now();
            }
            if (count($da)) {
                Addon::insert($da);
            }
        }
    }
}
