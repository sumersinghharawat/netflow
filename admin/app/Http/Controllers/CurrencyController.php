<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestsCurrencyUpdate;
use App\Models\CurrencyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CurrencyController extends CoreInfController
{
    public function index()
    {
        $currencies = CurrencyDetail::Active()->get();

        return view('admin.settings.advancedSettings.currency.index', compact('currencies'));
    }

    public function currencyUpdate(RequestsCurrencyUpdate $request, $id)
    {
        try {
            $currency = CurrencyDetail::find($id);
            // TODO ADD VALIDATION

            $currency->update([
                'title' => $request->title,
                'code' => $request->code,
                'value' => $request->value,
                'symbol_left' => $request->symbol,

            ]);

            return redirect()->route('currency')
                ->with('success', 'Currency updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('currency')
                ->with('error', $e->getMessage());
        }
    }

    public function currencyEdit($id)
    {
        $currency = CurrencyDetail::findOrFail($id);
        if ($currency->status == 0) {
            return redirect()->back()->with('error', 'you dont have access to edit that page');
        }

        return view('admin.settings.advancedSettings.currency._edit', compact('currency'));
    }

    public function currencyStatusChange($id)
    {
        $currency = CurrencyDetail::find($id);

        if ($currency->status == 1) {
            $currency->update([
                'status' => 0,
            ]);
        } elseif ($currency->status == 0) {
            $currency->update([
                'status' => 1,
            ]);
        }

        return redirect()->route('currency')
            ->with('success', 'Currency updated successfully.');
    }

    public function setDefault($currency)
    {
        CurrencyDetail::where('default', 1)->update(['default' => 0]);
        $currency = CurrencyDetail::findOrFail($currency)->update(['default' => 1, 'value' => 1]);
        if (Cache::has(auth()->user()->id.'_default_currency')) {
            Cache::forever(auth()->user()->id.'_default_currency', $currency);
        } else {
            Cache::forever(auth()->user()->id.'_default_currency', $currency);
        }

        return redirect()->route('currency')
            ->with('success', 'Default Currency changed successfully.');
    }

    public function changeCurrency(Request $request)
    {
        $currency = CurrencyDetail::findOrFail($request->currency);
        $user = auth()->user();
        $prefix = config('database.connections.mysql.prefix');
        $user->default_currency = $currency->id;
        if ($user->save()) {
            Cache::forever($prefix.'userCurrency', $currency);
        }

        return true;
    }
}
