<?php

namespace App\Imports;

use App\Http\Controllers\CoreInfController as inf;
use App\Models\Configuration;
use App\Models\Package;
use App\Models\PaymentGatewayConfig;
use App\Models\User;
use App\Services\UserApproveService;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserRegisterImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param  Collection  $collection
     */
    public function collection(Collection $collection)
    {
        $reg = new UserApproveService;
        $userData = [];
        foreach ($collection as $key => $row) {
            $productData = Package::where('product_id', $row['package_id'])->where('type', 'registration')->first();
            $sponsorId = User::where('username', $row['sponsor'])->first();
            $paymentType = PaymentGatewayConfig::where('slug', 'free-joining')->first()->id;
            $regAmount = Configuration::first()['reg_amount'];
            $totalAmount = round($productData['price']) + $regAmount;

            $userData[$key] = [
                'product_id' => $productData['id'],
                'username' => $row['username'],
                'position' => $row['position'] ?? '',
                'date_of_birth' => $row['dob'],
                'email' => $row['email'],
                'mobile' => $row['mobile'],
                'password' => $row['password'],
                'first_name' => $row['name'],
                'sponsor_id' => $sponsorId['id'],
                'reg_amount' => Configuration::first()['reg_amount'],
                'totalAmount' => $totalAmount,
                'payment_method' => $paymentType,
                'regFromTree' => 0,
                'product_amount' => $productData->price ?? 0,
                'product_pv'    => $productData->pair_value ?? 0,
            ];
            $pendingUser = $reg->addPendingRegistration($userData[$key], $paymentType, 0);
            $invoiceNO = $reg->generateSalesInvoiceNumber();
            $reg->addToSalesOrder($userData[$key], $invoiceNO, $pendingUser);
        }

        return $userData;
    }

    public function rules(): array
    {
        $inf = new inf;
        $moduleStatus = $inf->moduleStatus();

        return [
            'username' => 'required|unique:users,username|unique:pending_registrations,username',
            'package_id' => 'requiredIf:' . $moduleStatus->product_status . ',1' . '|exists:packages,product_id',
            'email' => 'required|email',
            'sponsor' => 'required|exists:users,username',
            'position' => Rule::requiredIf($moduleStatus['mlm_plan'] == 'Binary'),
        ];
    }
}
