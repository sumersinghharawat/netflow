<?php

namespace Database\Seeders;

use App\Models\SignupField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SignupFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('signup_fields')->delete();
        $data = [
            [
                'name' => 'first_name',
                'deafult_value' => 'First Name',
                'type' => 'text',
                'required' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1,

            ],
            [
                'name' => 'last_name',
                'type' => 'text',
                'deafult_value' => 'Last Name',
                'required' => 0,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 0,
            ],
            [
                'name' => 'date_of_birth',
                'type' => 'date',
                'deafult_value' => null,

                'required' => 1,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1,

            ],
            [
                'name' => 'gender',
                'type' => 'text',
                'deafult_value' => null,

                'required' => 1,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1,

            ],
            [
                'name' => 'address_line1',
                'type' => 'text',
                'deafult_value' => 'Address Line 1',
                'required' => 0,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 0,

            ],
            [
                'name' => 'address_line2',
                'type' => 'text',
                'deafult_value' => 'Address LIne 2',
                'required' => 0,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 0,

            ],
            [
                'name' => 'country',
                'type' => 'text',
                'deafult_value' => null,

                'required' => 0,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 0,

            ],
            [
                'name' => 'state',
                'type' => 'text',
                'deafult_value' => null,

                'required' => 0,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 0,

            ],
            [
                'name' => 'city',
                'type' => 'text',
                'deafult_value' => 'city',
                'required' => 0,
                'sort_order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 0,

            ],
            [
                'name' => 'pin',
                'type' => 'number',
                'deafult_value' => null,
                'required' => 0,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 0,

            ],
            [
                'name' => 'email',
                'type' => 'email',
                'deafult_value' => 'email@info.com',
                'required' => 1,
                'sort_order' => 11,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1,

            ],
            [
                'name' => 'mobile',
                'type' => 'number',
                'deafult_value' => 99999999,
                'required' => 1,
                'sort_order' => 12,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1,

            ],
        ];

        SignupField::insert($data);
    }
}
