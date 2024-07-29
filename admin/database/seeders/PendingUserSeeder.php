<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\ModuleStatus;
use App\Models\Package;
use App\Models\PaymentGatewayConfig;
use App\Models\PendingRegistration;
use App\Models\SignupSetting;
use App\Models\User;
use App\Services\UserApproveService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PendingUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $moduleStatus = ModuleStatus::first();
        for ($i = 1; $i <= 20; $i++) {
            $user = User::with('userDetail', 'userRegDetails')->where('user_type', 'admin')->get()->random();
            $package = Package::ActiveRegPackage()->get()->random();
            $config = Configuration::first();
            $totalAmount = round($config->reg_amount + $package->price);
            $payment = PaymentGatewayConfig::where('slug', 'free-joining')->first();
            $username = $this->getUsername();
            $position = ['L', 'R'];
            shuffle($position);
            $signupSettings = SignupSetting::first();
            $defaultCountry = $signupSettings->default_country;
            $password = Hash::make('password');
            $data = [
                'sponsorName' => $user->username,
                'sponsorFullname' => $user->userDetail->name.''.$user->userDetail->second_name,
                'position' => ($moduleStatus->mlm_plan != 'Unilevel' && $moduleStatus->mlm_plan != 'Stair_Step') ? $position[0] : null,
                'sponsor_id' => $user->id,
                'product_id' => $package->id,
                'age_limit' => 18,
                'mlm_plan' => $moduleStatus->mlm_plan,
                'username_type' => 'static',
                'productStatus ' => 'yes',
                'default_country' => 99,
                'sponsor_id' => $user->id,
                'first_name' => $this->faker->name,
                'last_name' => $this->faker->name,
                'date_of_birth' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
                'email' => $this->faker->unique()->safeEmail(),
                'mobile' => $this->faker->phoneNumber,
                'username' => $username,
                'password' => $password,
                'password_confirmation' => $password,
                'payment_method' => $payment->id,
                'terms' => 'yes',
                'date_of_joining' => Carbon::now()->format('Y-m-d H:i:s'),
                'totalAmount' => $totalAmount,
            ];

            if ($moduleStatus->mlm_plan == 'Unilevel' || $moduleStatus->mlm_plan == 'Stair_Step') {
                $data['placement_user_name'] = $user->username;
            }

            $pendingData = PendingRegistration::create([
                'username' => $username,
                'package_id' => $package->id,
                'sponsor_id' => $user->id,
                'data' => json_encode($data),
                'payment_method' => $payment->id,
                'date_added' => Carbon::now()->format('Y-m-d H:i:s'),
                'email_verification_status' => 'no',
            ]);

            // $regData                 = json_decode($pendingData->data, true);

            // $serviceClass            = new UserApproveService;
            // $productData             = Package::RegistrationPack($regData['product_id'])->ActivePackage()->first();

            // $placementData           = $serviceClass->getPlacementData($regData['position'], $regData['sponsor_id']);
            // $fatherData              = User::find($placementData->id)->load('ancestors');

            // $user = new User();

            // $user->username                 =  $regData['username'];
            // $user->user_type                =  'user';
            // $user->password                 =  Hash::make($regData['password']);
            // $user->position                =  $regData['position'];
            // $user->leg_position            = ($regData['position'] == "R") ? 2 : 1;
            // $user->father_id               =  $placementData->fatherId;
            // $user->sponsor_id              =  $regData['sponsor_id'];
            // $user->product_id              =  $regData['product'];
            // $user->date_of_joining         =  now();
            // $user->user_level              =  $fatherData->user_level + 1;
            // $user->personal_pv             =  $productData->pair_value;
            // $user->created_at              =  now();
            // $user->save();

            // $user_id        =   $user->id;
            // $user_details   =   array(
            //     'sponsor_id'        =>  $pendingData->sponsor_id,
            //     'country_id'        =>  $regData['defaultCountry'] ?? $defaultCountry,
            //     'name'              =>  $regData['first_name'],
            //     'second_name'       =>  $regData['last_name'],
            //     'DOB'               =>  $regData['date_of_birth'],
            //     'email'             =>  $regData['email'],
            //     'mobile'            =>  $regData['mobile'],
            //     'join_date'         =>  now(),
            //     'gender'            =>  $regData['gender'] ?? 'M'
            // );
            // $user->userDetails()->create($user_details);

            // $user_reg_details   =   array(
            //     'username'          =>  $regData['username'],
            //     'name'              =>  $regData['first_name'],
            //     'second_name'       =>  $regData['last_name'],
            //     'reg_amount'        =>  $regData['registration_fee'] ?? $config->reg_amount,
            //     'product_id'        =>  $regData['product'],
            //     'product_pv'        =>  $productData->pair_value,
            //     'product_amount'    =>  $productData->price,
            //     'reg_amount'        =>  $config->reg_amount,
            //     'total_amount'      =>  $totalAmount,
            //     'email'             =>  $regData['email'],
            //     'country_id'        =>  $regData['defaultCountry'] ?? $defaultCountry,
            //     'payment_method'    =>  $pendingData['payment_method'],
            // );
            // $user->userRegDetails()->create($user_reg_details);
            // $tran_pas   =   Hash::make(random_int(100000, 999999));
            // $user->transPassword()->create([
            //     'password'  =>  $tran_pas
            // ]);

            // $user->legDetails()->create([
            //     'total_left_count'  => 0
            // ]);
            // $treepathInsertData         = $fatherData->ancestors;
            // $res                        = [];
            // foreach ($treepathInsertData as $key => $da) {
            //     $res[$da->id] = ['level' => $user->user_level - $da->user_level];
            // }

            // $user->ancestors()->sync($res);

            // $pendingData->update([
            //     'status'                =>  'active',
            //     'updated_id'            =>  $user->id,
            //     'date_modified'         =>  now(),
            // ]);
        }
    }

    protected function getUsername()
    {
        $username = 'INF'.rand(000000, 99999);
        if (User::where('username', $username)->exists()) {
            return $this->getUsername();
        }

        return $username;
    }
}
