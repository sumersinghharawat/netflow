<?php

namespace Database\Seeders;

use App\Models\Rank;
use App\Models\Package;
use App\Models\OCProduct;
use App\Models\ModuleStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ranks')->delete();
        $moduleStatus = ModuleStatus::first();
        $ocprod1 = $ocprod2 = $ocprod3 = null;
        $prod1 = $prod2 = $prod3 = null;
        if($moduleStatus->ecom_status) {
            $pack = OCProduct::ActiveRegProduct();
            $ocprod1 = $pack->first()->product_id;
            $ocprod2 = $pack->skip(1)->first()->product_id;
            $ocprod3 = $pack->nth(3, 2)->first()->product_id;

        } else {
            $pack = Package::ActiveRegPackage()->get();
            $prod1  = $pack->first()->id;
            $prod2  = $pack->skip(1)->first()->id;
            $prod3  = $pack->nth(3, 2)->first()->id;
        }


        $ranks = collect([
            [
                'name' => 'Bronze',
                'color' => '#cd7f32',
                'tree_icon' => config('app.url').'assets/images/rank/bronze-medal.png',
                'image' => config('app.url').'assets/images/rank/bronze-medal.png',
                'commission' => 3,
                'package_id' => $prod1,
                'oc_product_id' => $ocprod1,
                'rank_order' => 1
            ],
            [
                'name' => 'Silver',
                'color' => '#C0C0C0',
                'tree_icon' => config('app.url').'assets/images/rank/silver-medal.png',
                'image' => config('app.url').'assets/images/rank/silver-medal.png',
                'commission' => 5,
                'package_id' => $prod2,
                'oc_product_id' => $ocprod2,
                'rank_order' => 2
            ],
            [
                'name' => 'Gold',
                'color' => '#FFD700',
                'tree_icon' => config('app.url').'assets/images/rank/Starter.png',
                'image' => config('app.url').'assets/images/rank/Starter.png',
                'commission' => 7,
                'package_id' => $prod3,
                'oc_product_id' => $ocprod3,
                'rank_order' => 3
            ],
            // [
            //     'name' => 'Platinum',
            //     'color' => '#e5e4e2',
            //     'tree_icon' => 'rank4.png',
            //     'commission' => 9,
            //     'package_id' => $pack->random(),
            //     'rank_order' => 4
            // ],
        ]);

        $ranks->map(fn ($rank) => Rank::create($rank));
    }
}
