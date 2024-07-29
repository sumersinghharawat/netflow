<?php

namespace Database\Seeders;

use App\Models\MatchingCommission;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatchingCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('matching_commissions')->delete();
        $package = Package::ActiveRegPackage()->get();
        $data = [];
        $key = 0;
        foreach ($package as $key => $pack) {
            $data[$key]['level'] = $key + 1;
            $data[$key]['package_id'] = $pack->id;
            $data[$key]['cmsn_member_pck'] = 5;
            $data[$key]['created_at'] = now();
            $data[$key]['updated_at'] = now();

            $key++;
        }

        // TODO matching commission seeder dynamic

        //     $data = [
        //         [
        //             'level'             => 1,
        //             'pck_id'            => 1,
        //             'cmsn_member_pck'   =>'5'
        //         ],
        //         [
        //             'level'             =>   1,
        //             'pck_id'            =>   2,
        //             'cmsn_member_pck'   =>  '6'
        //         ],
        //         [
        //             'level'             => 1,
        //             'pck_id'            =>   3,
        //             'cmsn_member_pck'   =>'7'
        //         ],
        //         [
        //             'level'          => 2,
        //             'pck_id'       => 1,
        //             'cmsn_member_pck'   =>'4'
        //         ],
        //         [
        //             'level'          => 2,
        //             'pck_id'       => 2,
        //             'cmsn_member_pck'   =>'5'
        //         ],
        //         [
        //             'level'          => 2,
        //             'pck_id'       => 3,
        //             'cmsn_member_pck'   =>'6'
        //         ],
        //         [
        //             'level'          => 3,
        //             'pck_id'       => 1,
        //             'cmsn_member_pck'   =>'3'
        //         ],
        //         [
        //             'level'          => 3,
        //             'pck_id'       => 2,
        //             'cmsn_member_pck'   =>'4'
        //         ],
        //         [
        //             'level'          => 3,
        //             'pck_id'       => 3,
        //             'cmsn_member_pck'   =>'5'
        //         ],

        //   ];
        MatchingCommission::insert($data);
    }
}
