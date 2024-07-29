<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Rank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseRankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purchase_ranks')->delete();
        $ranks = Rank::all()->load('downinePackCount');
        $packages = Package::ActiveRegPackage()->get();
        $ranks->map(fn ($rank, $k) => $rank->downinePackCount()->attach($packages->random(), ['count' => $k + 1]));
    }
}
