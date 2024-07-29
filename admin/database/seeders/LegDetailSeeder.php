<?php

namespace Database\Seeders;

use App\Models\LegDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('leg_details')->delete();
        $admin = User::GetAdmin();
        $da = LegDetail::create([
            'user_id' => $admin->id,
        ]);
    }
}
