<?php

namespace Database\Seeders;

use App\Models\SponsorTreepath;
use App\Models\Treepath;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TreepathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $guarded;

    public function run()
    {
        DB::table('treepaths')->delete();
        DB::table('sponsor_treepaths')->delete();
        $admin = User::GetAdmin();
        Treepath::create([
            'ancestor' => $admin->id,
            'descendant' => $admin->id,
        ]);

        SponsorTreepath::create([
            'ancestor' => $admin->id,
            'descendant' => $admin->id,
        ]);
    }
}
