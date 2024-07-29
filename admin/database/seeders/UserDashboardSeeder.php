<?php

namespace Database\Seeders;

use App\Models\UserDashboard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_dashboards')->delete();
        $parents = [
            [
                'name' => 'E wallet',
                'status' => 1,
                'slug' => Str::slug('E wallet'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Commission earned',
                'status' => 1,
                'slug' => Str::slug('Commission earned'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Payout released',
                'status' => 1,
                'slug' => Str::slug('Payout released'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Payout pending',
                'status' => 1,
                'slug' => Str::slug('Payout pending'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Donation',
                'status' => 0,
                'slug' => Str::slug('Donation'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Profile membership replica lcp',
                'status' => 1,
                'slug' => Str::slug('Profile membership replica lcp'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sponsor pv carry',
                'status' => 1,
                'slug' => Str::slug('sponsor pv carry'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'New members',
                'status' => 0,
                'slug' => Str::slug('New members'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Joinings graph',
                'status' => 1,
                'slug' => Str::slug('Joinings graph'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rank',
                'status' => 0,
                'slug' => Str::slug('Rank'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Earnings expenses',
                'status' => 1,
                'slug' => Str::slug('Earnings expenses'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Team performance',
                'status' => 1,
                'slug' => Str::slug('Team performance'),
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        UserDashboard::insert($parents);

        $parents    = UserDashboard::all();

        $children = collect([]);
        foreach ($parents as $key => $parent) {
            if($parent['slug'] == "earnings-expenses"){
                $children->push([
                        'name' => 'Earnings',
                        'status' => 1,
                        'slug' => Str::slug('Earnings'),
                        'parent_id' => $parent['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $children->push([
                        'name' => 'Expenses',
                        'status' => 1,
                        'slug' => Str::slug('Expenses'),
                        'parent_id' => $parent['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $children->push([
                        'name' => 'Payout status',
                        'status' => 1,
                        'slug' => Str::slug('Payout status'),
                        'parent_id' => $parent['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

            } elseif ($parent['slug'] == "team-performance") {
                $children->push([
                        'name' => 'Top earners',
                        'status' => 1,
                        'slug' => Str::slug('Top earners'),
                        'parent_id' => $parent['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                ]);
                $children->push([
                    'name' => 'Top recruiters',
                    'status' => 1,
                    'slug' => Str::slug('Top recruiters'),
                    'parent_id' => $parent['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                    $children->push([
                        'name' => 'Package overview',
                        'status' => 1,
                        'slug' => Str::slug('Package overview'),
                        'parent_id' => $parent['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $children->push([
                        'name' => 'Rank overview',
                        'status' => 0,
                        'slug' => Str::slug('Rank overview'),
                        'parent_id' => $parent['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        }
        UserDashboard::insert($children->toArray());
    }
}
