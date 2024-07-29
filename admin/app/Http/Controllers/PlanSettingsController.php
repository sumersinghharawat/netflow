<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonationConfigRequest;
use App\Http\Requests\MatrixConfigRequest;
use App\Jobs\UserActivityJob;
use App\Models\DonationConfiguration;
use App\Models\DonationRate;
use App\Models\StairStepConfig;
use Illuminate\Support\Facades\DB;
use Throwable;

class PlanSettingsController extends CoreInfController
{
    public function index($stairstepId = null)
    {
        $moduleStatus = $this->moduleStatus();
        $data = [];

        if ($moduleStatus['mlm_plan'] == 'Binary') {
            return abort(404);
        } elseif ($moduleStatus['mlm_plan'] == 'Donation') {
            $donations = DonationRate::get();
            $data += [
                'donations' => $donations,
                'configurtaion' => DonationConfiguration::first(),
            ];
        } elseif ($moduleStatus['mlm_plan'] == 'Stair_Step') {
            $stairstep = StairStepConfig::get();
            $stairstepSingle = ($stairstepId != null ? StairStepConfig::findOrFail($stairstepId) : null);

            $data += [
                'stairstep' => $stairstep,
                'stairstepSingle' => $stairstepSingle,
            ];
        }
        $configuration = $this->configuration();

        return view('admin.settings.planSettings.index', compact('moduleStatus', 'configuration', 'data'));
    }

    public function matrixConfigUpdate(MatrixConfigRequest $request)
    {
        try {
            $moduleStatus = $this->moduleStatus();
            $config = $this->configuration();
            if ($moduleStatus['mlm_plan'] == 'Matrix') {
                $user = auth()->user();
                $prefix = config('database.connections.mysql.prefix');

                $config->update([
                    'width_ceiling' => $request->width_ceiling,
                ]);

                UserActivityJob::dispatch(
                    auth()->user()->id,
                    [],
                    'configuration change',
                    $user->username.' changed width ceiling ',
                    $prefix,
                    auth()->user()->user_type,
                    $prefix,
                );



                return redirect()->back()->with('success', trans('planSettings.configUpdated'));
            }
        } catch (Throwable $th) {
            dd($th);
        }
    }

    public function donationConfigUpdate(DonationConfigRequest $request)
    {
        try {
            DB::beginTransaction();
            $donationRate = DonationRate::get();
            $user = auth()->user();
            $prefix = config('database.connections.mysql.prefix');

            foreach ($donationRate as $donation) {
                $donation->update([
                    'name' => $request->name[$donation->id],
                    'pm_rate' => $request->rate[$donation->id],
                    'referral_count' => $request->referral_count[$donation->id],
                ]);
            }

            $configuration = DonationConfiguration::first();

            $configuration->update([
                'donation_type' => $request->donation_type,
            ]);

            DB::commit();

            UserActivityJob::dispatch(
                $user->id,
                [],
                'configuration change',
                $user->username.' changed donation config',
                $prefix,
            );

            return redirect()->back()->with('success', trans('planSettings.donationSuccess'));
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
        }
    }
}
