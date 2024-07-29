<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\CoreInfController;
use App\Models\OCProduct;
use App\Models\Package;
use App\Models\Rank;

class ReferalCommissionController extends CoreInfController
{
    public function referralCommission()
    {
        $moduleStatus = $this->moduleStatus();
        $configuration = $this->configuration();
        $data = [
            'modulestatus' => $moduleStatus,
            'config' => $configuration,
            'currency' => currencySymbol(),
        ];
        if ($configuration->sponsor_commission_type == 'joinee_package' || $configuration->sponsor_commission_type == 'sponsor_package') {
            if ($moduleStatus->product_status) {
                $product_details = Package::ActiveRegPackage()->get(['id', 'product_id', 'name', 'referral_commission']);
                $data['level'] = $product_details;
            }
        }
        if ($moduleStatus->rank_status) {
            if ($configuration->sponsor_commission_type == 'rank') {
                $data += [
                    'rank' => $this->getActiveRankDetails(),
                ];
            }
        }
        if ($configuration->sponsor_commission_type == 'joinee_package' || $configuration->sponsor_commission_type == 'sponsor_package') {
            if ($moduleStatus->ecom_status) {
                $product_details = OCProduct::where('status', 1)->where('package_type', 'registration')->get(['product_id', 'model', 'referral_commission']);
                $data['level'] = $product_details;
            }
        }
        $return = view('ajax.referalCommission.referalCommission', compact('data'));

        return response()->json([
            'status' => true,
            'data' => $return->render(),
        ]);
    }

    public function levelCommissionPackage()
    {
        $moduleStatus = $this->moduleStatus();
        if ($moduleStatus->ecom_status) {
            $levelCommissionPackage = OCProduct::where('status', 1)->where('package_type', 'registration')->get(['product_id', 'model', 'referral_commission']);
        } else {
            $levelCommissionPackage = Package::ActiveRegPackage()->get(['id', 'product_id', 'name', 'referral_commission']);
        }
        $view = view('ajax.referalCommission.levelCommission', compact('levelCommissionPackage'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ]);
    }

    public function referralRank()
    {
        $activeRank = Rank::Active()->with('rankDetails')->get();
        $view = view('ajax.referalCommission.referalRank', compact('activeRank'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ]);
    }
}
