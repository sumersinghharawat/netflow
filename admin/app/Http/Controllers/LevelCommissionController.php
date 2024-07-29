<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonationLevelCommissionRequest;
use App\Http\Requests\GeneologyLevelCommissionRequest;
use App\Http\Requests\LevelCommissionRequest;
use App\Http\Requests\RequestslevelCommissionConfigUpdate;
use App\Jobs\UserActivityJob;
use App\Models\Configuration;
use App\Models\DonationRate;
use App\Models\GenelogyLevelCommission;
use App\Models\LevelCommission;
use App\Models\LevelCommissionRegisterPack;
use App\Models\OCProduct;
use App\Models\Package;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class LevelCommissionController extends CoreInfController
{
    public function index()
    {
        if (!$this->compensation()['sponsor_commission']) {
            return redirect()->back()->with(
                'error',
                'sponsor commission status is not activated'
            );
        }
        $geneologyLevel = [];
        $moduleStatus = $this->moduleStatus();
        $mlm_plan = $moduleStatus->mlm_plan;
        $configuration = $this->configuration();
        $levels = $configuration->commission_upto_level;

        if ($mlm_plan == 'Matrix' || $mlm_plan == 'Unilevel' || $moduleStatus->sponsor_commission_status) {
            if ($mlm_plan != 'Donation') {
                $geneologyLevel = GenelogyLevelCommission::LevelOrder()->get();
            }
        }
        $data = [];
        if ($mlm_plan == 'Donation') {
            $data['donationLevel'] = DonationRate::with('level')->get();
            $data['donationCount'] = LevelCommission::max('level_no');
        }
        $arr_level = $levels;
        if ($moduleStatus->ecom_status) {
            $packages = OCProduct::with('levelCommissionRegisterPack')->select('model as name', 'product_id as id')->where('package_type', 'registration')->get();
        } else {
            $packages = Package::ActiveRegPackage()->with('levelCommissionRegisterPack')->get();
        }
        $data['arr_level'] = $arr_level;
        $data['configuration'] = $configuration;
        $data['moduleStatus'] = $moduleStatus;
        $data['mlm_plan'] = $mlm_plan;
        $data['geneologyLevel'] = $geneologyLevel;
        $data['currency'] = currencySymbol();

        return view(
            'admin.settings.compensation.level.index',
            compact('data', 'levels', 'packages', 'moduleStatus')
        );
    }

    public function geneologyUpdate(GeneologyLevelCommissionRequest $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $moduleStatus = $this->moduleStatus();
            $compensation = $this->compensation();
            $mlm_plan = $moduleStatus->mlm_plan;
            $configuration = $this->configuration();
            $commissionType = $configuration->commission_criteria;
            if (in_array($mlm_plan, ['Matrix', 'Unilevel']) || $compensation->sponsor_commission) {
                if ($commissionType == 'genealogy' && $mlm_plan != 'Donation') {
                    $validatedData = $request->validated();
                    $insertData = [];
                    foreach ($validatedData['level_percentage'] as $key => $value) {
                        $insertData[$key]['level'] = $key;
                        if ($request->check_percentage == "percentage") {
                            $insertData[$key]['percentage'] = $value;
                        } else {
                            $insertData[$key]['percentage'] = defaultCurrency($value);
                        }
                        $insertData[$key]['created_at'] = now();
                        $insertData[$key]['updated_at'] = now();
                    }
                    if (count($insertData) > 0) {
                        GenelogyLevelCommission::truncate();
                        GenelogyLevelCommission::insert($insertData);
                        $user = auth()->user();
                        $prefix = config('database.connections.mysql.prefix');

                        UserActivityJob::dispatch(
                            $user->id,
                            [],
                            'compensation change',
                            $user->username . ' changed Level commission settings',
                            $prefix,
                            $user->user_type,
                        );
                    }
                }
            }
        } catch (Throwable $th) {
            return redirect()->back()->withErrors('Level Commission Updation failed');
        }

        return redirect()->back()->with('success', 'Level Commission Updated Succesfully');
    }

    public function configUpdate(RequestslevelCommissionConfigUpdate $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        DB::beginTransaction();
        try {
            $moduleStatus = $this->moduleStatus();
            $configuration = $this->configuration();
            $prev_level = $configuration->commission_upto_level ?? 0;
            $validatedData = $request->validated();
            $data = [
                'commission_upto_level' => $validatedData['commission_upto_level'],
                'commission_criteria' => $validatedData['level_commission_criteria'],
                'level_commission_type' => $validatedData['level_commission_type'],
            ];
            if ($moduleStatus->xup_status) {
                $data['xup_level'] = $request->xup_level;
            }
            $updateConfig = $configuration->update($data);

            if ($updateConfig) {
                $configuration = Configuration::first();
                $prefix = config('database.connections.mysql.prefix');
                if (Cache::has("{$prefix}configurations")) {
                    Cache::forever("{$prefix}configurations", $configuration);
                }
                $levelCommissionData = [];
                if ($data['commission_upto_level'] > $prev_level) {
                    for ($i = $prev_level + 1; $i <= $data['commission_upto_level']; $i++) {
                        $result = GenelogyLevelCommission::create([
                            'level' => $i,
                            'percentage' => '0',
                        ]);
                        if (!$result) {
                            break;
                        }
                    }
                } elseif ($data['commission_upto_level'] < $prev_level) {
                    for ($i = $prev_level; $i > $data['commission_upto_level']; $i--) {
                        $deleteLevel[] = $i;
                    }
                    GenelogyLevelCommission::whereIn('level', $deleteLevel)->delete();
                } else {
                    $checkExist = GenelogyLevelCommission::where('level', $prev_level);
                    if (!$checkExist->exists()) {
                        $genealogyLevel = new GenelogyLevelCommission;
                        $genealogyLevel->level = $prev_level;
                        $genealogyLevel->commission = 0;
                        $genealogyLevel->save();
                    }
                }

                $levelCommissionData = [];
                if ($moduleStatus->ecom_status) {
                    $package = OCProduct::select('product_id as id', 'model as name')->with('levelCommissionRegisterPack')->where('package_type', 'registration')->get();
                } else {

                    $package = Package::ActiveRegPackage()->get();
                }

                if ($data['commission_upto_level'] > $prev_level) {
                    $key = 0;
                    foreach ($package as $pack) {
                        for ($i = $data['commission_upto_level']; $i > $prev_level; $i--) {
                            $key++;
                            if ($moduleStatus->ecom_status) {
                                $levelCommissionData[$key]['oc_product_id'] = $pack->id;
                            } else {
                                $levelCommissionData[$key]['package_id'] = $pack->id;
                            }
                            $levelCommissionData[$key]['level'] = $i;
                            $levelCommissionData[$key]['commission'] = 0;
                            $levelCommissionData[$key]['percentage'] = 0;
                            $levelCommissionData[$key]['created_at'] = now();
                            $levelCommissionData[$key]['updated_at'] = now();
                        }
                    }
                    LevelCommissionRegisterPack::insert($levelCommissionData);
                } elseif ($data['commission_upto_level'] < $prev_level) {
                    for ($i = $prev_level; $i > $data['commission_upto_level']; $i--) {
                        $deleteLevelRegPack[] = $i;
                    }
                    LevelCommissionRegisterPack::whereIn('level', $deleteLevelRegPack)->delete();
                } else {
                    $checkExist = LevelCommissionRegisterPack::where('level', $prev_level);
                    if ($checkExist->exists()) {
                        $presentPackage = $checkExist->get()->pluck('package_id');
                        $missionPackage = $package->whereNotIn('id', $presentPackage);
                        foreach ($missionPackage as $misiongPack) {
                            if ($moduleStatus->ecom_status) {
                                $levelCommissionData[$misiongPack->id]['oc_product_id'] = $misiongPack->id;
                            } else {
                                $levelCommissionData[$misiongPack->id]['package_id'] = $misiongPack->id;
                            }
                            $levelCommissionData[$misiongPack->id]['level'] = $prev_level;
                            $levelCommissionData[$misiongPack->id]['commission'] = 0;
                            $levelCommissionData[$misiongPack->id]['percentage'] = 0;
                            $levelCommissionData[$misiongPack->id]['created_at'] = now();
                            $levelCommissionData[$misiongPack->id]['updated_at'] = now();
                        }
                    } else {
                        foreach ($package as $pack) {
                            if ($moduleStatus->ecom_status) {
                                $levelCommissionData[$pack->id]['oc_product_id'] = $pack->id;
                            } else {
                                $levelCommissionData[$pack->id]['package_id'] = $pack->id;
                            }
                            $levelCommissionData[$pack->id]['level'] = $prev_level;
                            $levelCommissionData[$pack->id]['commission'] = 0;
                            $levelCommissionData[$pack->id]['percentage'] = 0;
                            $levelCommissionData[$pack->id]['created_at'] = now();
                            $levelCommissionData[$pack->id]['updated_at'] = now();
                        }
                    }
                    LevelCommissionRegisterPack::insert($levelCommissionData);
                }
            }

            DB::commit();
            $user = auth()->user();
            $prefix = config('database.connections.mysql.prefix');

            UserActivityJob::dispatch(
                $user->id,
                [],
                'compensation change',
                $user->username . ' changed Level commission settings',
                $prefix,
                $user->user_type,
            );

            if (Cache::has("{$prefix}configurations")) {
                $configuration = Configuration::first();
                Cache::forever("{$prefix}configurations", $configuration);
            }

            return redirect(route('levelcommission'))->with('success', 'level commission config updated');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect(route('levelcommission'))
                ->withErrors($th->getMessage());
        }
    }

    public function update(LevelCommissionRequest $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $data = [];
            $moduleStatus = $this->moduleStatus();
            DB::table('level_commission_register_packs')->delete();
            foreach ($request->levelpack as $key => $value) {
                $data[$key] = [
                    'level' => $value['level'],
                    'commission' => $value['commission'],
                    'package_id' => ($moduleStatus->ecom_status ? null : $value['package_id']),
                    'oc_product_id' => ($moduleStatus->ecom_status ? $value['package_id'] : null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            LevelCommissionRegisterPack::insert($data);
        } catch (\Throwable $th) {
            dd($th);
        }

        return redirect()->back()->with('success', 'configuration updated');
    }

    public function donationLevelUpdate(DonationLevelCommissionRequest $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            DB::table('level_commissions')->delete();
            foreach ($request->leveldonation as $key => $donationLevel) {
                $data[$key] = [
                    'level' => $donationLevel['donationRate_id'],
                    'level_no' => $donationLevel['level'],
                    'percentage' => $donationLevel['percentage'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            LevelCommission::insert($data);
            $user = auth()->user();
            $prefix = config('database.connections.mysql.prefix');

            UserActivityJob::dispatch(
                $user->id,
                [],
                'compensation change',
                $user->username . ' changed Donation Level commission settings',
                $prefix,
                $user->user_type,
            );

            return redirect()->back()->with('success', 'Donation Level Settings Updated Succesfully..');
        } catch (\Throwable $th) {
            // dd($th);
            throw $th;
        }
    }
}
