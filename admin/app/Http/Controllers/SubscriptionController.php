<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBankPaymentReceiptRequest;
use App\Models\Package;
use App\Models\Packagevalidityextendhistory;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentReceipt;
use App\Models\SubscriptionConfig;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\PackageUpgradeService;
use App\Services\UserApproveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadTraits;

class SubscriptionController extends CoreInfController
{
    use UploadTraits;

    public function index($userId)
    {
        if (!$userId)
            return redirect(route('profile.view'))->with('error', 'user details required');

        $user = User::with('package')->FindOrFail($userId);
        $subscriptionConfig = SubscriptionConfig::first();
        $paymentGateways = PaymentGatewayConfig::Renewal()->get();
        if ($this->moduleStatus()['subscription_status'] == 1 && $subscriptionConfig->based_on == 'amount_based') {
            $product_amount = $subscriptionConfig->fixed_amount;
        } else {
            $product_amount = $user->package->price;
        }
        $currency = currencySymbol();
        return view('subscriptionRenew.index', compact('user', 'product_amount', 'paymentGateways', 'currency'));
    }
    public function renewSubmit(Request $request)
    {
        DB::beginTransaction();
        try {
            $approveService = new UserApproveService;
            $packageService = new PackageUpgradeService;
            $data = $request->except('_method', '_token');
            $user = auth()->user();
            $paymentType = PaymentGatewayConfig::findOrfail($data['payment_method']);
            $paymentStatus = $approveService->checkPaymentMethod($this->moduleStatus(), $paymentType, $data, $user, $user, 'renew');
            $bankPayment = PaymentGatewayConfig::where('slug', 'bank-transfer')->first();

            if ($paymentStatus || $paymentType->slug == "bank-transfer") {
                $packageValidityExtentHistoryId = Packagevalidityextendhistory::max('id');
                $packageService->packageValidityUpgrade($packageValidityExtentHistoryId, $request->product_id, $request->user_id, $request->totalAmount, $request->payment_method, $this->moduleStatus());
                DB::commit();
                return redirect(route('profile.view',['username' => $request->user_id]))->with('success', 'Package renewed successfully');
            }
        } catch (Throwable $th) {
            DB::rollBack();
            return redirect(route('profile.view'))->with('error', $th->getMessage());
        }
    }
    public function addPaymentReceipt(AddBankPaymentReceiptRequest $request)
    {
        $file = $request->file('reciept');
        $prefix = 'bnk_';
        $folder = 'reciept';
        //$model      = $pendingData;
        $fileName = $this->uploadBnkRcpt(compact('file', 'prefix', 'folder'));
        $host = request()->getSchemeAndHttpHost();

        $user = User::where('username', $request->user_name)->first();
        PaymentReceipt::create([
            'receipt' => $host . "/storage/$folder/$fileName",
            'username' => $request->user_name,
            'type' => 'renewal',
            'user_id' => $user->id,
        ]);

        return response()->json(['success' => 'Receipt added successfully.']);
    }
}
