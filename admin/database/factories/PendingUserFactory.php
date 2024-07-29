<?php

namespace Database\Factories;

use App\Models\Configuration;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PendingUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::with('userDetail')->get()->random();
        $package = Package::where('type_of_package', 'registration')->get()->random();
        $config = Configuration::first();
        $totalAmount = round($config->reg_amount + $package->price);
        $username = $this->faker->unique()->userName;
        $data = [
            'sponsorName' => $user->username,
            'sponsorFullname' => $user->userDetail->name.''.$user->userDetail->second_name,
            'position' => 'L',
            'product_id' => $package->id,
            'age_limit' => 18,
            'mlm_plan' => 'Binary',
            'username_type' => 'static',
            'productStatus ' => 'yes',
            'defaultCountry' => 99,
            'sponsor_id' => $user->id,
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'date_of_birth' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile' => $this->faker->phoneNumber,
            'username' => $username,
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => 'yes',
            'date_of_joining' => Carbon::now()->format('Y-m-d H:i:s'),
            'totalAmount' => $totalAmount,
        ];

        return [
            'username' => $username,
            'package_id' => $package->id,
            'sponsor_id' => $user->id,
            'data' => json_encode($data),
        ];
    }
}
