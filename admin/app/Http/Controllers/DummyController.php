<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Faker\Factory;
use App\Models\User;
use App\Models\Country;
use App\Models\Package;
use App\Models\OCProduct;
use App\Models\SignupField;
use App\Models\UsernameConfig;
use App\Models\PaymentGatewayConfig;
use App\Services\UserApproveService;
use Illuminate\Support\Facades\Hash;
use App\Services\PackageUpgradeService;
use App\Http\Controllers\CoreInfController;

class DummyController extends CoreInfController
{
    public function insert($count = 0)
    {
        $moduleStatus       = $this->moduleStatus();

        for ($i = 0; $i <= $count; $i++) {
            $insertData         = $this->getInsertData($moduleStatus);
            if ($insertData['product_id'] != '') {
                $servicePackageupgrade = new PackageUpgradeService;
                $product_validity = $servicePackageupgrade->getPackageValidityDate($insertData['product_id'], '', $moduleStatus);
            } else {
                $product_validity = '';
            }
            $approveService     = new UserApproveService;
            $approveStatus      = $approveService->confirmRegister($insertData->toArray(), $moduleStatus, $product_validity);
        }
        return redirect()->route('dashboard')->withSuccess('Insertion Completed');
    }
    protected function getInsertData($moduleStatus)
    {
        $faker               = Factory::create();
        $insertData          = collect([]);
        $sponsor             = User::whereNotIn('user_type', ['employee', 'reentry'])->where('active', 1)->where('delete_status', 1)->with('userDetail')->get()->random();
        $registerationFields = SignupField::where('status', 1)->get();
        $country             = Country::has('states')->get()->random();
        $position            = null;
        if ($moduleStatus->mlm_plan == 'Binary')
            $position        = collect(['L', 'R'])->random();

        foreach ($registerationFields as $key => $value) {
            if ($value->name == 'first_name')
                $insertData->put($value->name, $faker->firstName);
            elseif ($value->name == 'last_name')
                $insertData->put($value->name, $faker->lastName);
            elseif ($value->name == 'gender')
                $insertData->put($value->name, collect(['M', 'F', 'O'])->random());
            elseif ($value->type == 'date')
                $insertData->put($value->name, Carbon::now()->subYear(20)->format('Y-m-d'));
            elseif ($value->type == 'email')
                $insertData->put($value->name, $faker->email);
            elseif ($value->type == 'number')
                $insertData->put($value->name, $faker->randomNumber);
            elseif ($value->name == 'country')
                $insertData->put($value->name, $country->id);
            elseif ($value->name == 'state')
                $insertData->put($value->name, $country->states->random()->id);
            elseif ($value->type == 'text')
                $insertData->put($value->name, $faker->sentence);
            elseif ($value->type == 'textarea')
                $insertData->put($value->name, $faker->sentence);
        }
        $insertData->put('regFromTree', 0);
        $insertData->put('sponsorName', $sponsor->username);
        $insertData->put('sponsorFullname', $sponsor->userDetail->name . ' ' . $sponsor->userDetail->second_name);
        $insertData->put('sponsor_id', $sponsor->id);
        $insertData->put('placement_username', $insertData['sponsorName']);
        $insertData->put('placement_fullname', $insertData['sponsorFullname']);
        $mlmplan    = $moduleStatus->mlm_plan;
        // if ($mlmplan == 'Binary') {
        $insertData->put('position', $position);
        // }
        if ($moduleStatus->product_status && !$moduleStatus->ecom_status) {
            $package    = Package::ActiveRegPackage()->get()->random();
        } elseif ($moduleStatus->ecom_status) {
            $package    = OCProduct::ActiveRegProduct()->random();
        }
        $insertData->put('product_id', $package->id);

        $usernameConfig = UsernameConfig::first();
        $length = explode(';', $usernameConfig->length);
        $minLength = $length[0];
        $maxLength = $length[1];
        $username = generateUsername($minLength, $maxLength);
        $insertData->put('username', $username);
        $insertData->put('password', Hash::make('123456'));
        $insertData->put('password_confirmation', Hash::make('123456'));
        $insertData->put('terms', 'yes');
        $insertData->put('totalAmount', $package->price ?? 0 + $this->configuration()['reg_amount']);
        $insertData->put('reg_amount', $this->configuration()['reg_amount']);
        $insertData->put('product_amount', $package->price);
        $insertData->put('product_pv', $package->pair_value ?? 0);

        $insertData->put('payment_method', PaymentGatewayConfig::where('slug', 'free-joining')->first()->id);
        return $insertData;
    }
}
