<?php

namespace App\Http\Controllers;

use App\Http\Requests\PinNumberRequset;
use App\Models\PinAmountDetails;
use App\Models\PinConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class EpinConfigController extends CoreInfController
{
    public function index()
    {
        $pin_status = $this->moduleStatus()['pin_status'];
        if ($pin_status) {
            $config = PinConfig::first();
            $pinAmount = PinAmountDetails::AscOrder()->get();

            return view('admin.settings.advancedSettings.epin.index', compact('config', 'pinAmount'));
        } else {
            return redirect()->back()->with('error', 'pin configuration not activated');
        }
    }

    public function update(Request $request, $id)
    {
        // TODO add employee activity
        // TODO add config history table
        DB::beginTransaction();
        try {
            $config = PinConfig::find($id);

            $config->update([
                'character_set' => $request->character_set,
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
        DB::commit();

        return redirect(route('pinconfig.index'))->with('success', 'configuration updated');
    }

    public function pinNumberAdd(PinNumberRequset $request)
    {
        try {
            PinAmountDetails::create([
                'amount' => $request->amount,
            ]);
        } catch (Throwable $th) {
            return back()->with('error', $th->getMessage());
        }

        return redirect(route('pinconfig.index'))->with('success', 'pin amount added succesfully');
    }

    public function pinNumberDelete(Request $request)
    {
        try {
            $ids = $request->amount;
            $amountDetails = PinAmountDetails::GetIds([...$ids])->get();

            foreach ($amountDetails as $amount) {
                $amount->delete();
            }
        } catch (Throwable $th) {
            return back()->with('error', $th->getMessage());
        }

        return redirect(route('pinconfig.index'))->with('success', 'pin amount deleted succesfully');
    }
}
