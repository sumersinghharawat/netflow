<?php

namespace App\Imports;

use App\Models\Configuration;
use App\Models\ModuleStatus;
use App\Models\OCProduct;
use App\Models\User;
use App\Services\EcomService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EcomRegisterImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $ecomService = new EcomService;
        foreach ($collection as $key => $user) {
            $sponsor = User::where('username', $user['sponsor'])->first();
            $productData = OCProduct::where('model', $user['product_model'])->first();
            $regAmount = Configuration::first()['reg_amount'];
            $totalAmount = round($productData['price']) + $regAmount;
            $country = DB::table('oc_country')->where('name', $user['country'])->first();
            $order_id = $ecomService->addOrderTables($user, $country, $totalAmount);

            $ecomService->addToOctempRegistration($user, $sponsor, $productData, $country, $totalAmount, $order_id);
        }

        return true;
    }

    public function rules(): array
    {
        $moduleStatus = ModuleStatus::first();
        return [
            'username' => 'required|unique:users,username|unique:oc_temp_registration,user_name',
            'product_model' => 'requiredIf:' . $moduleStatus->product_status . ',1' . '|exists:oc_product,model',
            'email' => 'required|email',
            'sponsor' => 'required|exists:users,username',
            'position' => Rule::requiredIf($moduleStatus['mlm_plan'] == 'Binary'),
            'address' => 'required',
            'city' => 'required',
            'post_code' => 'required',
            'country' => 'required|exists:oc_country,name',
        ];
    }
}
