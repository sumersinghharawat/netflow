<?php

namespace Database\Seeders;

use App\Models\PasswordPolicy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasswordPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('password_policies')->delete();
        PasswordPolicy::create([
            'enable_policy' => 0,
            'mixed_case' => 1,
            'number' => 1,
            'sp_char' => 1,
            'min_length' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
