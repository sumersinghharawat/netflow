<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReplicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ReplicaBannerSeeder::class,
            ReplicaContentSeeder::class,
        ]);
    }
}
