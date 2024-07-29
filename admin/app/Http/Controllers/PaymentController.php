<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestPaymentUpdate;
use App\Http\Requests\RequestsPayment;
use App\Jobs\UserActivityJob;
use App\Models\BankTransferSettings;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentGatewayDetail;
use App\Services\StripeService;
use Illuminate\Support\Facades\Cache;

class PaymentController extends CoreInfController
{
    public function index()
    {
        $moduleStatus = $this->moduleStatus();
        $data = [
            'moduleStatus' => $moduleStatus,
            'paymentConfig' => PaymentGatewayConfig::SortAscOrder()->with('details')->whereNotIn('id',array(1,6,7))->get(),
            // 'paymentConfig' => PaymentGatewayConfig::SortAscOrder()->with('details')->whereNotIn('id',array(1,6,7))->get(),
            'bankdetails' => BankTransferSettings::first(),
        ];
        return view('admin.settings.payment.index', compact('data', 'moduleStatus'));
    }

    public function bankDetailUpdate(RequestsPayment $request, $id)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $settings = BankTransferSettings::find($id);
            $settings->update([
                'account_info' => $request->accountInfo,
            ]);

            return redirect(route('payment.view'))->with('success', 'Bank Details Updated');
        } catch (\Exception $e) {
            return redirect(route('payment.view'))
            ->with('error', $e->getMessage());
        }
    }

    public function update(RequestPaymentUpdate $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }
        $data = $request->validated();
        try {
            $payment = PaymentGatewayConfig::get();
            foreach ($payment as $settings) {
                $status = 'status_'.$settings->id;
                $registration = 'registartion_'.$settings->id;
                $repurchase = 'repurchase_'.$settings->id;
                $membership = 'renewal_'.$settings->id;
                $upgrade = 'upgradation_'.$settings->id;
                $admin = 'admin_'.$settings->id;

                PaymentGatewayConfig::where('id', $settings->id)->update([
                    'status' => $request->$status ?? 0,
                    'registration' => $request->$registration ?? 0,
                    'repurchase' => $request->$repurchase ?? 0,
                    'membership_renewal' => $request->$membership ?? 0,
                    'upgradation' => $request->$upgrade ?? 0,
                    'admin_only' => $request->$admin ?? 0,
                ]);
            }
            $user = auth()->user();
            $prefix = config('database.connections.mysql.prefix');
            UserActivityJob::dispatch(
                $user->id,
                $data,
                'PaymentGateway changed',
                $user->username.'changed PaymentGateway Settings',
                $prefix,
                $user->user_type
            );
            if (Cache::has("{$prefix}PaymentGatewayConfig")) {
                Cache::forever("{$prefix}PaymentGatewayConfig", $payment);
            }

            return redirect(route('payment.view'))->with('success', 'payment gateway config updated');
        } catch (\Exception $e) {
            return redirect(route('payment.view'))
            ->with('error', $e->getMessage());
        }
    }
    public function getStripeKey()
    {
        $serviceClass = new StripeService;
        return response()->json([
            'status' => true,
            'data'  => $serviceClass->getStripeCredentials()
        ]);
    }
}
