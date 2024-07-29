<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('company_profiles')->delete();
        CompanyProfile::create([
            'name' => 'company name',
            'email' => 'company@company.com',
            'phone' => '123456789',
            'address' => 'company address',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
