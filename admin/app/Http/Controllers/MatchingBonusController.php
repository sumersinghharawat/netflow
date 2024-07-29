<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestsGenealogyUpdate;
use App\Http\Requests\RequestsMatching;
use App\Jobs\UserActivityJob;
use App\Models\Configuration;
use App\Models\MatchingCommission;
use App\Models\MatchingLevelCommission;
use App\Models\Package;
use App\Services\BonusService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MatchingBonusController extends CoreInfController
{
    public function index()
    {
        if (! $this->compensation()['matching_bonus']) {
            return redirect()->back()->with(
                'error',
                'matching bonus commission status is not activated'
            );
        }
        $moduleStatus = $this->moduleStatus();
        $configuration = $this->configuration();
        $matchingGenealogyCommission = MatchingLevelCommission::LevelAscOrder()->get();
        $packages = null;
        $matchingCommissionPack = null;
        if ($moduleStatus->product_status || ($moduleStatus->ecom_status && $moduleStatus->ecom_status_demo)) {
            $packages = Package::ActiveRegPackage()
                                        ->get(['id', 'product_id', 'name', 'referral_commission']);
            $matchingPackageCommission = MatchingCommission::all();
        }

        return view('admin.settings.compensation.matchingBonus.index', compact('moduleStatus', 'configuration', 'matchingGenealogyCommission', 'packages', 'matchingPackageCommission'));
    }

    public function update(RequestsMatching $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            DB::beginTransaction();
            $data = $request->validated();
            $configuration = $this->configuration();
            $currentLevel = $configuration->matching_upto_level;
            $moduleStatus = $this->moduleStatus();

            $query = $configuration->update([
                'matching_criteria' => $request->matching_criteria,
                'matching_upto_level' => $request->matching_upto_level,
            ]);
            $configuration = Configuration::first();
            $prefix = config('database.connections.mysql.prefix');
            Cache::forever("{$prefix}configurations", $configuration);

            $user = auth()->user();
            $prefix = config('database.connections.mysql.prefix');
            UserActivityJob::dispatch(
                $user->id,
                $data,
                'MatchingBonus config',
                $user->username.'changed MatchingBonus configuration',
                $prefix,
                $user->user_type,
            );
            if (Cache::has("{$prefix}configurations")) {
                Cache::forever("{$prefix}configurations", $configuration);
            }
            if ($query) {
                $newLevel = $request->matching_upto_level;
                if ($currentLevel != $newLevel) {
                    if ($newLevel < $currentLevel) {
                        MatchingLevelCommission::where('level_no', '>', $newLevel)->delete();
                    }
                    if ($moduleStatus->product_status || ($moduleStatus->ecom_status && $moduleStatus->ecom_status_demo)) {
                        if ($newLevel < $currentLevel) {
                            $matchingCommission = MatchingCommission::where('level', '>', $newLevel)->delete();
                        }
                    }
                }

                // TODO INSERT EMPLOYEE ACTIVITY if user type  is an employee
                // TODO insert User Activity
                // TODO insert config change history table with what changes made
                // Reference  $this->insertConfigChangeHistory('commission settings', $history);
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();

        return redirect()->route('matching_bonus')
            ->with('success', 'Matching criteria and level updated successfully.');
    }

    public function commissionUpdate(RequestsGenealogyUpdate $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $configuration = $this->configuration();
            $insertArray = [];
            $now = now();

            if ($configuration->matching_criteria == 'genealogy') {
                MatchingLevelCommission::truncate();
                $level = array_keys($request->level_percentage);
                foreach ($level as $key => $value) {
                    $insertArray[$key]['level_no'] = $value;
                    $insertArray[$key]['level_percentage'] = (int) $request->level_percentage[$value];
                    $insertArray[$key]['created_at'] = $now;
                    $insertArray[$key]['updated_at'] = $now;
                }
                MatchingLevelCommission::insert($insertArray);
            } elseif ($configuration->matching_criteria == 'member_pck') {
                MatchingCommission::truncate();
                $k = 0;
                foreach ($request->commission as $packId => $levelCommission) {
                    $level = array_keys($levelCommission);
                    foreach ($level as $key => $lev) {
                        $insertArray[$k]['level'] = $lev;
                        $insertArray[$k]['package_id'] = $packId;
                        $insertArray[$k]['cmsn_member_pck'] = $levelCommission[$lev];
                        $insertArray[$k]['created_at'] = $now;
                        $insertArray[$k]['updated_at'] = $now;
                        $k++;
                    }
                }
                MatchingCommission::insert($insertArray);
            } else {
                return abort(404);
            }
            $user = auth()->user();
            $prefix = config('database.connections.mysql.prefix');
            UserActivityJob::dispatch(
                $user->id,
                [],
                'MatchingBonus level config',
                $user->username.'changed MatchingBonus level configuration',
                $prefix,
                $user->user_type,
            );

            return redirect()->back()->with('success', 'matching bonus updated');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
