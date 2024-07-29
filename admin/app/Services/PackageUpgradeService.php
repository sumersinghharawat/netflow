<?php

namespace App\Services;

use App\Models\OCProduct;
use App\Models\Package;
use App\Models\PackageUpgradeHistory;
use App\Models\Packagevalidityextendhistory;
use App\Models\PaymentGatewayConfig;
use App\Models\UpgradesalesOrder;
use App\Models\User;
use PhpParser\Node\Expr\Throw_;

class PackageUpgradeService
{
    public function isUpgradablePackage($package_id)
    {
        $current_package_price = $this->getCurrentPackagePrice($package_id);
        $packages = Package::where('type', 'registration')->where('price', '>', $current_package_price)->pluck('id')->toArray();

        return $packages;
    }

    public function getCurrentPackagePv($current_package_id)
    {
        $package = Package::find($current_package_id);

        return $package->pair_value;
    }

    public function getNewPackagePv($new_package_id)
    {
        $package = Package::find($new_package_id);

        return $package->pair_value;
    }

    public function getCurrentPackagePrice($package_id)
    {
        $package = Package::find($package_id);

        return $package->price;
    }

    public function getNewPackagePrice($new_package_id)
    {
        $package = Package::find($new_package_id);

        return $package->price;
    }

    public function getRoi($new_package_id)
    {
        $package = Package::find($new_package_id);

        return $package->roi;
    }

    public function getRoiDays($new_package_id)
    {
        $package = Package::find($new_package_id);

        return $package->days;
    }

    public function getPackagePrice($new_package_id)
    {
        $package = Package::find($new_package_id);

        return $package->price;
    }

    public function getProductPvByPackageId($package_id, $moduleStatus)
    {
        if ($moduleStatus->ecom_status == 0) {
            $package = Package::where('id', $package_id)->first();

            return $package->pair_value;
        } else {
            $package = OCProduct::where('product_id', $package_id)->first();

            return $package->pair_value;
        }
    }

    public function getProductPackageId($product_id, $moduleStatus, $packageType)
    {
        if ($moduleStatus->ecom_status) {
            $packageId = OCProduct::where('package_type', $packageType)->where('product_id', $product_id)->first();

            return $packageId;
        }
        $packageId = Package::where('type', $packageType)->where('id', $product_id)->first();

        return $packageId;
    }

    public function getValidityDate($userId)
    {
        $user = User::where('id', $userId)->first();
        if ($user->product_validity != null) {
            return $user->product_validity;
        } else {
            return 0;
        }
    }

    public function getPackageValidityDate($newPackageId, $validity_date, $moduleStatus)
    {
        if ($moduleStatus->ecom_status || $moduleStatus->ecom_status_demo) {
            $package = OCProduct::find($newPackageId);
        } else {
            if ($moduleStatus->ecom_status || $moduleStatus->ecom_status_demo) {
                $package = OCProduct::find($newPackageId);
            } else {
                $package = Package::where('id', $newPackageId)->first();
            }
        }
        $expiryDate = $this->calculateProductValidity($package->validity, $validity_date);

        return $expiryDate;
    }

    public function calculateProductValidity($validity, $validity_date = '')
    {
        if ($validity_date == '') {
            $validity_date = date('Y-m-d H:i:s');
        }
        //$current_date_time = date('Y-m-d H:i:s');
        $month_validity = '+' . $validity . ' month';
        $time = strtotime($validity_date);
        $product_validity = date('Y-m-d H:i:s', strtotime($month_validity, $time));

        return $product_validity;
    }

    public function packageValidityUpgrade($packageValidityExtentHistoryId, $new_package_id, $userId, $totalAmount, $paymentType, $moduleStatus)
    {
        $today = date('Y-m-d H:i:s');
        $invoice_no = 1000 + $packageValidityExtentHistoryId;
        $invoice_no = 'VLDPCK' . $invoice_no;
        $product_pv = $this->getProductPvByPackageId($new_package_id, $moduleStatus);
        $bankPayment = PaymentGatewayConfig::where('slug', 'bank-transfer')->first();
        if ($paymentType == $bankPayment->id) {
            $packagevalidityextendhistory = Packagevalidityextendhistory::create([
                'user_id' => $userId,
                'invoice_id' => $invoice_no,
                'total_amount' => $totalAmount,
                'product_pv' => $product_pv,
                'payment_type' => $paymentType,
                'pay_type' => 'manual',
                'renewal_status' => 0,
            ]);
            if ($moduleStatus->ecom_status) {
                $packagevalidityextendhistory->update([
                    'oc_product_id' => $new_package_id
                ]);
            } else {
                $packagevalidityextendhistory->update([
                    'package_id' => $new_package_id,
                ]);
            }
        } else {
            $packagevalidityextendhistory = Packagevalidityextendhistory::create([
                'user_id' => $userId,
                'invoice_id' => $invoice_no,
                'total_amount' => $totalAmount,
                'product_pv' => $product_pv,
                'payment_type' => $paymentType,
                'pay_type' => 'manual',
                'renewal_status' => 1,
            ]);
            if ($moduleStatus->ecom_status) {
                $packagevalidityextendhistory->update([
                    'oc_product_id' => $new_package_id
                ]);
            } else {
                $packagevalidityextendhistory->update([
                    'package_id' => $new_package_id,
                ]);
            }
            if ($packagevalidityextendhistory) {
                $result = $invoice_no;
                $validity_date = $this->getValidityDate($userId);
                if ($validity_date < $today) {
                    $expiry_date = $this->getPackageValidityDate($new_package_id, '', $moduleStatus);
                } else {
                    $expiry_date = $this->getPackageValidityDate($new_package_id, $validity_date, $moduleStatus);
                }
                User::find($userId)->update([
                    'product_validity' => $expiry_date,
                ]);
            }
        }
    }

    public function addToPackageUpgradeHistory($data)
    {
        if($data['payment_type'] === 'bank_transfer') $paymentType = PaymentGatewayConfig::where('slug', 'bank-transfer')->first()->id;
        if($data['payment_type'] === 'cod') $paymentType = PaymentGatewayConfig::where('slug', 'cod')->first()->id;
        if($data['payment_type'] === 'epin') $paymentType = PaymentGatewayConfig::where('slug', 'e-pin')->first()->id;
        if($data['payment_type'] === 'ewallet') $paymentType = PaymentGatewayConfig::where('slug', 'e-wallet')->first()->id;
        if($data['payment_type'] === 'free_upgrade') $paymentType = PaymentGatewayConfig::where('slug', 'cod')->first()->id;

        $data['payment_type'] = $paymentType;
        try {
            $history = new PackageUpgradeHistory();
            $history->fill($data);
            $history->save();

            return ['status' => true];
        } catch (\Throwable $th) {
            return ['status' => false, 'data' => $th->getMessage()];
            // throw $th;
        }
    }

    public function addToUpgradeSalesOrder($data)
    {
        try {
            $order = new UpgradesalesOrder();
            $order->user_id  = $data['user_id'];
            $order->amount   = $data['payment_amount'];
            $order->total_pv = $data['pv_difference'];
            $order->payment_method = $data['payment_type'];
            $order->oc_product_id = $data['oc_new_package_id'];
            $order->save();
            return ['status' => true];
        } catch (\Throwable $th) {
            return ['status' => false, 'data' => $th->getMessage()];
            throw $th;
        }
    }

    public function getPackageDetails($id, $moduleStatus)
    {
        if ($moduleStatus->ecom_status) {
            $package = OCProduct::find($id);
            return $package ?? null;
        }
    }
}
