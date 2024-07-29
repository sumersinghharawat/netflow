<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\RepurchaseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $re = RepurchaseCategory::all();
        DB::table('packages')->delete();
        $data = [
            [
                'name' => 'Membership Pack1',
                'type' => 'registration',
                'product_id' => 'product-1',
                'price' => 100,
                'bv_value' => 50,
                'pair_value' => 50,
                'quantity' => 0,
                'referral_commission' => 2,
                'pair_price' => 5,
                'days' => 5,
                'roi' => 10.00,
                'joinee_commission' => 0,
                'validity' => 5,
                'tree_icon' => "",
                'reentry_limit' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Membership Pack2',
                'type' => 'registration',
                'product_id' => 'product-2',
                'price' => 300,
                'bv_value' => 50,
                'pair_value' => 100,
                'quantity' => 0,
                'referral_commission' => 5,
                'pair_price' => 7,
                'days' => 5,
                'roi' => 10.00,
                'validity' => 3,
                'joinee_commission' => 0,
                'tree_icon' => "",
                'reentry_limit' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Membership Pack3',
                'type' => 'registration',
                'product_id' => 'product-3',
                'price' => 500,
                'bv_value' => 50,
                'pair_value' => 150,
                'quantity' => 0,
                'referral_commission' => 8,
                'pair_price' => 10,
                'days' => 5,
                'roi' => 10.00,
                'validity' => 4,
                'joinee_commission' => 0,
                'tree_icon' => "",
                'reentry_limit' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];

        Package::insert($data);
    }
}
