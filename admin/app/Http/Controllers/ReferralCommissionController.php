<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestsReferralCommissionUpdate;
use App\Jobs\UserActivityJob;
use App\Models\OCProduct;
use App\Models\Package;
use App\Models\RankDetail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReferralCommissionController extends CoreInfController
{
    public function referralCommission()
    {
        $configuration = $this->configuration();
        $compensation = $this->compensation();
        if (! $compensation->referral_commission) {
            return redirect()->back()->with(
                'error',
                'Referral commission status not activated'
            );
        }
        $moduleStatus = $this->moduleStatus();
        $data = [
            'module_status' => $moduleStatus,
            'configuration' => $configuration,
        ];
        if ($moduleStatus->rank_status) {
            $rank_details = $this->getActiveRankDetails();
            $data += [
                'rank_details' => $rank_details,
            ];
        }
        if ($moduleStatus->product_status) {
            if ($moduleStatus->ecom_status) {
                $product_details = OCProduct::where('status', 1)->where('package_type', 'registration')->get(['product_id', 'model', 'referral_commission']);
                $data += [
                    'product_details' => $product_details,
                ];
            } else {
                $product_details = Package::ActiveRegPackage()->get(['id', 'product_id', 'name', 'referral_commission']);
                $data += [
                    'product_details' => $product_details,
                ];
            }
        }

        return view(
            'admin.settings.compensation.referal.index',
            compact('data','configuration')
        );
    }

    public function referralCommissionUpdate(RequestsReferralCommissionUpdate $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }
        // dd($request->all());
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $configuration = $this->configuration();
            $moduleStatus = $this->moduleStatus();

            if ($this->moduleStatus()->sponsor_commission_status) {
                $data = [
                    'referral_commission_type' => $request->referral_commission_type,
                    'sponsor_commission_type' => $request->sponsor_commission_type,
                ];
            }
            if ($request->sponsor_commission_type != 'rank' && ($moduleStatus->product_status || $moduleStatus->ecom_status)) {
                if ($this->moduleStatus()->ecom_status) {
                    $package_details = OCProduct::where('status', 1)->where('package_type', 'registration')->get();
                } else {
                    $package_details = Package::ActiveRegPackage()->get();
                }
                // dd($request->product);
                foreach ($package_details as $key => $package) {
                    if ($request->referral_commission_type == 'flat') {
                        $package->update([
                            'referral_commission' => defaultCurrency($request->product[$package->id] ?? $request->product[$package->product_id]),
                        ]);
                    } else {
                        $package->update([
                            'referral_commission' => $request->product[$package->id] ?? $request->product[$package->product_id],
                        ]);
                    }
                }
                $configuration->update($data);
           } elseif($moduleStatus->rank_status ){
                $rank_details = RankDetail::all();
                foreach ($rank_details as $rank) {
                    $rank->update(['referral_commission' => $request->rank[$rank->id]]);
                }
                $configuration->update($data);
            } else {
                $configuration->update(['referral_amount' => $request->referral_amount]);
            }

            DB::commit();
            $prefix = config('database.connections.mysql.prefix');
            Cache::forever("{$prefix}configurations", $configuration);

            $user = auth()->user();
            $prefix = config('database.connections.mysql.prefix');
            UserActivityJob::dispatch(
                $user->id,
                $data,
                'Referral config',
                $user->username.'changed Referral configuration',
                $prefix,
                $user->user_type
            );
            if (Cache::has("{$prefix}configurations")) {
                Cache::forever("{$prefix}configurations", $configuration);
            }
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect(route('referralcommission'))
            ->with(
                'success',
                'Referral commission updated succesfully'
            );
    }
}
