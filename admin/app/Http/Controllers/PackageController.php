<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Package;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Models\PaypalProducts;
use App\Models\StripeProducts;
use App\Services\PaypalService;
use App\Services\StripeService;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentGatewayConfig;
use App\Http\Requests\RequestPackageAddnew;
use App\Http\Requests\RequestPackageNewPackage;
use Illuminate\Http\Exceptions\PostTooLargeException;

class PackageController extends CoreInfController
{
    use UploadTraits;

    public function index(Request $request)
    {
        $moduleStatus = $this->moduleStatus();

        if (!$moduleStatus->product_status) {
            return redirect()->back()->withErrors(trans('common.not_configured'));
        }
        $status = $request->status;
        $packages = Package::ActiveRegPackage()->with('stripe', 'paypal')->paginate(10)->withQueryString();
        if ($status == 'blocked') {
            $packages = Package::BlockRegPackage()->with('stripe', 'paypal')->paginate(10)->withQueryString();
        }
        // $pvVisible = 'no';
        $bvVisible = 'no';
        $moduleStatus = $this->moduleStatus();
        $mlmPlan = $moduleStatus->mlm_plan;
        $compensationStatus = $this->compensation();
        $configuration = $this->getConfig();
        $commissionType = $configuration['sponsor_commission_type'];
        $stripeStatus = PaymentGatewayConfig::select('membership_renewal')->where('slug', 'stripe')->Renewal()->first();
        $paypalStatus = PaymentGatewayConfig::select('membership_renewal')->where('slug', 'paypal')->Renewal()->first();
        // if ($mlmPlan == 'Binary') {
        $pvVisible = 'yes';
        // }
        // if ($mlmPlan == 'Unilevel' || $mlmPlan == 'Matrix' || $mlmPlan == 'Stair_Step' || ($moduleStatus->sponsor_commission_status && $mlmPlan != 'Binary')) {
        //     $bvVisible = 'yes';
        // }
        $currency = currencySymbol();

        return view('admin.package.membership.index', compact(
            'packages',
            'pvVisible',
            'bvVisible',
            'mlmPlan',
            'moduleStatus',
            'compensationStatus',
            'configuration',
            'commissionType',
            'currency',
            'stripeStatus',
            'paypalStatus'
        ));
    }

    public function store(RequestPackageAddnew $request, $id = null)
    {
        if(session()->get('is_preset')){
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }
        $validatedData = $request->validated();
        $oldprice = Package::where('id',$id)->first()->price??0;
        if ($validatedData['price'] != $oldprice)
        {
            $leastprice = Package::where('pair_value','<',$request->pairValue)
                        ->where('type','repurchase')
                        ->max('price');
            $maxprice = Package::where('pair_value','>',$request->pairValue)
                        ->where('type','repurchase')
                        ->min('price');
            if ((!$maxprice && $validatedData['price'] > $leastprice) || (!$leastprice && $validatedData['price'] < $maxprice) || ($validatedData['price'] > $leastprice && $validatedData['price'] < $maxprice)) {
                $price = $validatedData['price'];
            } else {
                return response()->json([
                    'status' => 'error',
                    'errors' => 'Price is not in range.',
                ], 401);
            }
        }

        DB::beginTransaction();
        try {
            
            $moduleStatus = $this->moduleStatus();
            // $pvVisible = 'no';
            $bvVisible = 'no';
            $mlmPlan = $moduleStatus->mlm_plan;
            $compensationStatus = $this->compensation();
            $configuration = $this->getConfig();
            $commissionType = $configuration['sponsor_commission_type'];
            $type = ($request->has('type') ? 'repurchase' : 'registration');
            // if ($mlmPlan == 'Binary') {
            $pvVisible = 'yes';
            // }
            // if ($mlmPlan == 'Unilevel' || $mlmPlan == 'Matrix' || $mlmPlan == 'Stair_Step' || ($moduleStatus->sponsor_commission_status == 'yes' && $mlmPlan != 'Binary')) {
            //     $bvVisible = 'yes';
            // }

            if ($pvVisible == 'yes') {
                $pairValue = $validatedData['pairValue'];
            }

            if ($bvVisible == 'yes') {
                $bvValue = $validatedData['bvValue'];
            }
            if ($mlmPlan == 'Binary' && $compensationStatus->plan_commission) {
                $pairPrice = $validatedData['pairPrice'] ?? 0;
            }
            if ($moduleStatus->referral_status && $compensationStatus->referral_commission) {
                if ($commissionType == 'sponsor_package' || $commissionType == 'joinee_package') {
                    $referralCommission = $validatedData['referralCommission'] ?? 0;
                }
            }

            if ($moduleStatus->subscription_status && $type == 'registration') {
                $validity = $validatedData['validity'];
            }

            if ($moduleStatus->roi_status && $compensationStatus->roi_commission && $type == 'registration') {
                $roi = $validatedData['roi'];
                $days = $validatedData['days'];
            }
            if ($mlmPlan == 'Monoline') {
                $reentryLimit = $validatedData['reentry_limit'] ?? 0;
            }

            $package = Package::updateOrCreate([
                'id' => $id,
            ], [
                'name' => $validatedData['name'],
                'type' => $type,
                'product_id' => $validatedData['product_id'],
                'price' => defaultCurrency($validatedData['price']),
                'bv_value' => $bvValue ?? 0,
                'pair_value' => $pairValue ?? 0,
                'quantity' => $validatedData['quantity'] ?? 0,
                'referral_commission' => defaultCurrency($referralCommission ?? 0),
                'pair_price' => defaultCurrency($pairPrice ?? 0),
                'roi' => $roi ?? 0,
                'days' => $days ?? 0,
                'validity' => $validity ?? 0,
                'reentry_limit' => $reentryLimit ?? 0,
                'description' => $validatedData['description'] ?? null,
                'category_id' => $validatedData['category'] ?? null,
            ]);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $model = $package;
                $prefix = 'repurchase-';
                $folder = 'packages';
                if (!$this->singleFileUpload($file, $model, $prefix, $folder)) {
                    DB::rollback();
                    if ($request->ajax()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'file upload failed.',
                        ], 400);
                    }

                    return redirect()->back()->withErrors('file upload failed.');
                }
            }
            DB::commit();
            if (empty($id)) {
                $message = 'product created succesfully';
            } else {
                $message = 'product updated succesfully';
            }
            if ($request->ajax()) {
                $packages = Package::ActiveRegPackage()->paginate(10);
                $currency = currencySymbol();
                $view = view('admin.package.membership.ajax.newPackage', compact('pvVisible', 'bvVisible', 'packages', 'moduleStatus', 'currency'));

                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'data' => $view->render(),
                ]);
            } else {
                return redirect()->back()->with('success', $message);
            }
        } catch (\Exception $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 404);
            } else {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }

    public function packageEdit($id)
    {
        $package = Package::find($id);
        // $pvVisible = 'no';
        $bvVisible = 'no';
        $moduleStatus = $this->moduleStatus();
        $mlmPlan = $moduleStatus->mlm_plan;
        $compensationStatus = $this->compensation();
        $configuration = $this->getConfig();
        $commissionType = $configuration['sponsor_commission_type'];
        $currency = currencySymbol();

        // if ($mlmPlan == 'Binary') {
        $pvVisible = 'yes';
        // }
        // if ($mlmPlan == 'Unilevel' || $mlmPlan == 'Matrix' || $mlmPlan == 'Stair_Step' || ($moduleStatus->sponsor_commission_status == 'yes' && $mlmPlan != 'Binary')) {
        //     $bvVisible = 'yes';
        // }

        $view = view('admin.package.membership._edit', compact('package', 'pvVisible', 'bvVisible', 'mlmPlan', 'moduleStatus', 'compensationStatus', 'commissionType', 'currency'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ]);
    }

    public function packageStatusChange(Request $request, $id)
    {
        if(session()->get('is_preset')){
            return redirect()->route('package')
                    ->with('error', "You don't have permission By using Preset Demo");
        }
        try {
            $count = Package::where('active', 1)
            ->where('type', 'registration')
            ->count();
                $package = Package::find($id);
                if ($package->active) {
                    if ($count > 1 ){
                        $package->update([
                            'active' => 0,
                        ]);
                        $message = 'package inactivated';
                    }else{
                        $message = 'At least one package need to be activated';
                        return redirect()->back()
                        ->with('error', $message);
                    }
                } else {
                    $package->update([
                        'active' => 1,
                    ]);
                    $message = 'package activated';
                }
                if ($request->ajax()) {
                    return response()->json([
                        'status' => true,
                        'message' => $message,
                    ]);
                } else {
                    return redirect()->back()
                        ->with('success', $message);
                }
        } catch (Throwable $th) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 400);
            } else {
                return redirect()->route('package')
                    ->with('error', $message);
            }
        }
    }

    public function getConfig()
    {
        $config = Configuration::get([
            'commission_upto_level', 'commission_criteria',
            'level_commission_type', 'matching_upto_level',
            'sales_level', 'sponsor_commission_type',
        ])->first();

        return $config;
    }
    public function createPaymentId(Request $request)
    {
        try {
            $stripeService = new StripeService;
            $paypalService = new PaypalService;

            if ($request->slug == 'stripe') {
                $isProductExist = StripeProducts::where('product_id', $request->package['id'])->count();
                if (!$isProductExist) {
                    $response = $stripeService->createStripeProduct($request->package);
                } else {
                    $response = response()->json([
                        'status' => false,
                        'message' => 'product ID Already created',
                    ]);
                }
            }

            if ($request->slug == 'paypal') {
                $isProductExist = PaypalProducts::where('product_id', $request->package['id'])->count();
                if (!$isProductExist) {
                    $response = $paypalService->createPaypalProduct($request->package);
                } else {
                    $response = response()->json([
                        'status' => false,
                        'message' => 'product ID Already created',
                    ]);
                }
            }

            if (isset($response) && $response) {
                return $response;
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'product ID creation has been failed.',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function storeNewPackage(RequestPackageNewPackage $request, $id = null)
    {
        if(session()->get('is_preset')){
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }
        $validatedData = $request->validated();
        $leastprice = Package::where('pair_value','<',$request->pairValue)
                    ->where('type','registration')
                    ->max('price');
        $maxprice = Package::where('pair_value','>',$request->pairValue)
                    ->where('type','registration')
                    ->min('price');
        if ((!$maxprice && $validatedData['price'] > $leastprice) || (!$leastprice && $validatedData['price'] < $maxprice) || ($validatedData['price'] > $leastprice && $validatedData['price'] < $maxprice)) {
            $price = $validatedData['price'];
        } else {
            return response()->json([
                'status' => 'error',
                'errors' => 'Price is not in range.',
            ], 401);
        }

        DB::beginTransaction();
        try {
            $moduleStatus = $this->moduleStatus();
            // $pvVisible = 'no';
            $bvVisible = 'no';
            $mlmPlan = $moduleStatus->mlm_plan;
            $compensationStatus = $this->compensation();
            $configuration = $this->getConfig();
            $commissionType = $configuration['sponsor_commission_type'];
            $type = ($request->has('type') ? 'repurchase' : 'registration');
            // if ($mlmPlan == 'Binary') {
            $pvVisible = 'yes';
            // }
            // if ($mlmPlan == 'Unilevel' || $mlmPlan == 'Matrix' || $mlmPlan == 'Stair_Step' || ($moduleStatus->sponsor_commission_status == 'yes' && $mlmPlan != 'Binary')) {
            //     $bvVisible = 'yes';
            // }

            if ($pvVisible == 'yes') {
                $pairValue = $validatedData['pairValue'];
            }

            if ($bvVisible == 'yes') {
                $bvValue = $validatedData['bvValue'];
            }
            if ($mlmPlan == 'Binary' && $compensationStatus->plan_commission) {
                $pairPrice = $validatedData['pairPrice'] ?? 0;
            }
            if ($moduleStatus->referral_status && $compensationStatus->referral_commission) {
                if ($commissionType == 'sponsor_package' || $commissionType == 'joinee_package') {
                    $referralCommission = $validatedData['referralCommission'] ?? 0;
                }
            }

            if ($moduleStatus->subscription_status && $type == 'registration') {
                $validity = $validatedData['validity'];
            }

            if ($moduleStatus->roi_status && $compensationStatus->roi_commission && $type == 'registration') {
                $roi = $validatedData['roi'];
                $days = $validatedData['days'];
            }
            if ($mlmPlan == 'Monoline') {
                $reentryLimit = $validatedData['reentry_limit'] ?? 0;
            }

            $package = Package::updateOrCreate([
                'id' => $id,
            ], [
                'name' => $validatedData['name'],
                'type' => $type,
                'product_id' => $validatedData['product_id'],
                'price' => defaultCurrency($price),
                'bv_value' => $bvValue ?? 0,
                'pair_value' => $pairValue ?? 0,
                'quantity' => $validatedData['quantity'] ?? 0,
                'referral_commission' => defaultCurrency($referralCommission ?? 0),
                'pair_price' => defaultCurrency($pairPrice ?? 0),
                'roi' => $roi ?? 0,
                'days' => $days ?? 0,
                'validity' => $validity ?? 0,
                'reentry_limit' => $reentryLimit ?? 0,
                'description' => $validatedData['description'] ?? null,
                'category_id' => $validatedData['category'] ?? null,
            ]);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $model = $package;
                $prefix = 'repurchase-';
                $folder = 'packages';
                if (!$this->singleFileUpload($file, $model, $prefix, $folder)) {
                    DB::rollback();
                    if ($request->ajax()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'file upload failed.',
                        ], 400);
                    }

                    return redirect()->back()->withErrors('file upload failed.');
                }
            }
            DB::commit();
            if (empty($id)) {
                $message = 'product created succesfully';
            } else {
                $message = 'product updated succesfully';
            }
            if ($request->ajax()) {
                $packages = Package::ActiveRegPackage()->paginate(10);
                $currency = currencySymbol();
                $view = view('admin.package.membership.ajax.newPackage', compact('pvVisible', 'bvVisible', 'packages', 'moduleStatus', 'currency'));

                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'data' => $view->render(),
                ]);
            } else {
                return redirect()->back()->with('success', $message);
            }
        } catch (\Exception $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 404);
            } else {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }

}
