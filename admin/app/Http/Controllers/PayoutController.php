<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestkycCategoryAdd;
use App\Http\Requests\RequestPaymentMethodUpdate;
use App\Http\Requests\RequestsPayoutUpdate;
use App\Jobs\UserActivityJob;
use App\Models\KycCategory;
use App\Models\PaymentGatewayConfig;
use App\Models\PayoutConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class PayoutController extends CoreInfController
{
    public function index()
    {
        $moduleStatus       = $this->moduleStatus();
        $configuration      = PayoutConfiguration::first();
        $payment_gateway    = PaymentGatewayConfig::Paymentonly()->Payoutascorder()->where('payout_status',1)->get();
        $kyc_categories     = KycCategory::get();
        $currency           = currencySymbol();
        return view('admin.settings.payout.index', compact('moduleStatus', 'configuration', 'payment_gateway', 'kyc_categories', 'currency'));
    }

    public function updatePayout(RequestsPayoutUpdate $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }
        $data = $request->validated();

        $data['min_payout'] = defaultCurrency($request->min_payout);
        $data['max_payout'] = defaultCurrency($request->max_payout);
        $data['fee_amount'] = defaultCurrency($request->fee_amount);
        $data['mail_status'] = $data['mail_status'] ?? 0;

        DB::beginTransaction();
        try {
            PayoutConfiguration::first()->update($data);
        } catch (Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
        DB::commit();

        return back()->with('success', 'record updated successfully.');
    }

    public function paymentMethodUpdate(RequestPaymentMethodUpdate $request)
    {
        $validated = $request->validated();
        try {
            $payment_gateway = PaymentGatewayConfig::Paymentonly()->Payoutascorder()->get();
            foreach ($payment_gateway as $key => $value) {
                $status = (in_array($value->id, $validated['method'])) ? 1 : 0;
                $value->update(['payout_status' => $status]);
            }
            return back()
                    ->with('success', 'payment method updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function kycCategory()
    {
        $kycCategories = KycCategory::all();

        return view('admin.settings.kyccategories', compact('kycCategories'));
    }

    public function kycCategoryAdd(RequestkycCategoryAdd $request)
    {
        $data = $request->validated();
        $data['status'] = 'Active';
        $KycCategory = new KycCategory;
        try {
            $KycCategory->fill($data);
            $KycCategory->save();
        } catch (\Throwable $e) {
            // TODO manage other than ajax request
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
        $user = auth()->user();
        $prefix = config('database.connections.mysql.prefix');
        UserActivityJob::dispatch(
            $user->id,
            $data,
            'Add KYC Category',
            $user->username.'Add KYC Category',
            $prefix,
            $user->user_type,
        );
        if (Cache::has("{$prefix}KycCategory")) {
            Cache::forever("{$prefix}KycCategory", $KycCategory);
        }
        if ($request->ajax()) {
            $view = view('admin.settings.payout.inc.addKycCategory', compact('KycCategory'));

            return response()->json([
                'status' => true,
                'message' => 'Kyc Category Created successfully',
                'data' => $view->render(),
            ]);
        } else {
            return redirect()->back()->with('success', 'Kyc Category created successfully.');
        }
    }

    public function kycCategoryEdit($id)
    {
        $KycCategory = KycCategory::find($id)->get();

        return view('admin.settings.kyccategories_edit', compact('KycCategory'));
    }

    public function kycCategoryUpdate(RequestkycCategoryAdd $request, $id)
    {
        try {
            KycCategory::find($id)->update(['category' => $request->category]);
            return redirect(route('payout'))
                ->with('success', 'Kyc Category Updated successfully');
        } catch (\Throwable $e) {
            return redirect(route('payout'))
                ->with('error', $e->getMessage());
        }
    }

    public function kycCategoryDelete(Request $request, $id)
    {
        try {
            KycCategory::find($id)->delete();
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Kyc Category Successfully Deleted.',
                ]);
            } else {
                return redirect()->back()->with([
                    'status' => true,
                    'message' => 'Kyc Category Successfully Deleted.',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => true,
                'message' => 'Error occured!',
            ], 404);
        }
    }
}
