<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\UserPlacement;
use App\Models\User;

class UserPlacementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('user_type', 'admin')->first();
        DB::table('user_placements');
        UserPlacement::create([
            'user_id' => $user->id,
        ]);
    }
}
