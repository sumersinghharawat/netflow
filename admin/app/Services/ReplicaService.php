<?php

namespace App\Services;

use App\Http\Controllers\CoreInfController;
use App\Models\Country;
use App\Models\DemoUser;
use App\Models\PaymentReceipt;
use App\Models\PinNumber;
use App\Models\SignupField;
use App\Models\State;
use App\Models\User;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\DB;

class ReplicaService extends CoreInfController
{
    use UploadTraits;

    public function addPaymentReceipt($file, $prefix, $folder, $username, $adminusername)
    {
        if(config('mlm.demo_status') == 'yes')
        {
            $prefix = $this->getprefix($adminusername);
            config(['database.connections.mysql.prefix' => "{$prefix}_"]);
            DB::purge('mysql');
            DB::connection('mysql');
        }

        $upload = $this->uploadBnkRcpt(compact('file', 'prefix', 'folder'));

        $host = request()->getSchemeAndHttpHost();

        PaymentReceipt::create([
            'receipt' => $host . "/storage/$folder/$upload",
            'receipt' => $upload,
            'username' => $username,
            'type' => 'register',
        ]);

        return $upload;
    }

    public function getprefix($username)
    {
        $prefix = '';
        $demoUser = DemoUser::where('username', $username)->first();
        if (! $demoUser) {
            abort(401);
        }
        $prefix = $demoUser->prefix;

        return $prefix;
    }

    public function getuserstate($username)
    {
        if(config('mlm.demo_status') == 'yes')
        {
            $prefix = $this->getprefix($username);
            config(['database.connections.mysql.prefix' => "{$prefix}_"]);
            DB::purge('mysql');
            DB::connection('mysql');
        }


        if ($username != null) {
            $countryId = User::where('username', $username)->with('userDetail')->first()->userDetail->country_id;
        } else {
            $countryId = auth()->user()->userDetail->country_id;
        }
        $data = [
            'state' => State::orderBy('name', 'ASC')->where('country_id', $countryId)->get(),
            'status' => $this->stateStatus(),
        ];

        return $data;
    }

    public function epinPayment($regData, $user)
    {
        $count = count($regData['epin']);
        for ($i = 0; $i <= $count - 1; $i++) {
            $pinNumber = $regData['epin'][$i];

            $epin = PinNumber::where('numbers', $pinNumber)->first();
            $balanceAmount = $epin->balance_amount;
            $str = 'epinUsedBalance_'.$epin->id;
            $balance = $balanceAmount - $regData[$str];

            if (! $balance) {
                $epin->update([
                    'status' => 'used',
                ]);
            }
            $epin->update([
                'balance_amount' => $balance,
                'used_user' => $user['id'],
                'is_used' => 1,
            ]);
        }

        return true;
    }

    public function ewalletPayment($moduleStatus, $regData, $user, $sponsorData)
    {
        $ewalletService = new EwalletService;

        $ewalletUser = $sponsorData;
        $transactionNumber = generateTransactionNumber();
        $amount = $regData['totalAmount'];

        $TransactionId = $this->insertTransaction($transactionNumber);

        $insertUsedWallet = $ewalletService->insertUsedEwallet($moduleStatus, $ewalletUser, $user, $amount, $TransactionId, 'replica_register');
        if ($insertUsedWallet) {
            $ewalletService->deductUserBalance($sponsorData, $amount);
        }

        return true;
    }

    public function stateStatus()
    {
        $state = SignupField::where('name', 'state')->first();

        return $state;
    }

    public function getstate($request, $country, $username)
    {
        if(config('mlm.demo_status') == 'yes')
        {
            $prefix = $this->getprefix($username);

            config(['database.connections.mysql.prefix' => "{$prefix}_"]);
            DB::purge('mysql');
            DB::connection('mysql');
        }

        $country = Country::find($country);
        $user = User::with('userDetails')->find($request->userId);

        $data = [
            'state' => $country->states,
            'status' => $this->stateStatus(),
            'user' => $user,
        ];

        return $data;
    }
}
