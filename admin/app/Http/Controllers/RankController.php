<?php

namespace App\Http\Controllers;

use App\Http\Requests\RankRequest;
use App\Http\Requests\RequestsRankUpdate;
use App\Jobs\UserActivityJob;
use App\Models\Rank;
use App\Models\RankConfiguration;
use App\Models\RankDetail;
use App\Traits\PackageTraits;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RankController extends CoreInfController
{
    use PackageTraits, UploadTraits;

    public function index()
    {
        $moduleStatus = $this->moduleStatus();
        $rankConfig = ($moduleStatus->product_status || $moduleStatus->ecom_status)
            ? RankConfiguration::all()
            : RankConfiguration::ProductIndependent()->get();
        $prefix = config('database.connections.mysql.prefix');
        $ranks = Rank::with('rankCriteria')->get();
        $commissionType = $this->configuration()['sponsor_commission_type'];
        $package = $this->regPackages();
        Cache::forget("{$prefix}rankActiveConfig");
        $activeConfig = (Cache::has("{$prefix}rankActiveConfig"))
            ? Cache::get("{$prefix}rankActiveConfig")
            : RankConfiguration::Active()->get();
        $configuration = $this->configuration();
        if ($activeConfig->contains('slug', 'downline-rank-count')) {
            $ranks->load('downlineRankCount');
        }
        if ($activeConfig->contains('slug', 'downline-package-count')) {
            $ranks->load('downinePackCount');
        }
        $currency = currencySymbol();

        return view('admin.settings.rank.index', compact('moduleStatus', 'rankConfig', 'ranks', 'commissionType', 'package', 'activeConfig', 'configuration', 'currency'));
    }

    public function rankConfigUpdate(RequestsRankUpdate $request)
    {

        if(session()->get('is_preset')){
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        // TODO configuration for other than BINARY Plan
        $validatedData = $request->validated();
        try {
            $moduleStatus = $this->moduleStatus();
            $configs = RankConfiguration::select('slug')
                ->whereIn('id', [...$validatedData['rankconfig']])
                ->get()->pluck('slug')->flip();

            if ($moduleStatus->product_status && $configs->has(['personal-pv', 'group-pv', 'joiner-package', 'downline-package-count'])) {
                return redirect()->back()->withErrors([
                    'error' => 'You have no products for enabling these rank criteria',
                ]);
            }

            RankConfiguration::where('status', 1)->update(['status' => 0]);
            RankConfiguration::whereIn('id', [...$validatedData['rankconfig']])->update(['status' => 1]);
            $configuration = $this->configuration();
            $configuration->update([
                'rank_calculation' => $validatedData['calculation'],
            ]);
            $activeConfig = RankConfiguration::Active()->get();

            $prefix = config('database.connections.mysql.prefix');
            Cache::forever("{$prefix}rankActiveConfig", $activeConfig);
        } catch (\Exception $e) {
            return redirect(route('rank'))
                ->with('error', $e->getMessage());
        }

        if ($request->ajax()) {
            $ranks = Rank::with('rankCriteria')->get();
            if ($activeConfig->contains('slug', 'downline-rank-count')) {
                $ranks->load('downlineRankCount');
            }
            if ($activeConfig->contains('slug', 'downline-package-count')) {
                $ranks->load('downinePackCount');
            }
            $currency = currencySymbol();
            return response()->json([
                'status' => true,
                'data' => view('admin.settings.rank.inc.table', compact('activeConfig', 'ranks', 'currency'))->render(),
                'message' => trans('rank.rank_config_update'),
            ]);
        }

        $user = auth()->user();
        $prefix = config('database.connections.mysql.prefix');
        UserActivityJob::dispatch(
            $user->id,
            $validatedData,
            'Rank config',
            $user->user_type,
            $user->username.'changed Rank config',
            $prefix
        );
        if (Cache::has("{$prefix}RankConfiguration")) {
            Cache::forever("{$prefix}RankConfiguration", $configs);
        }

        return redirect(route('rank'))
            ->with('success', trans('rank.rank_config_update'));
    }

    public function store(RankRequest $request)
    {
        $validatedData = $request->validated();

        $activeConfig = RankConfiguration::Active()->get();
        $currency = currencySymbol();

        DB::beginTransaction();
        try {
            $rank = new Rank;
            $rankDetails = new RankDetail;
            unset($validatedData['image']);
            $validatedData['commission'] = defaultCurrency($validatedData['commission']);
            $rank->name = $request->name;
            $rank->color = $request->color;
            $rank->oc_product_id = $request->package_id;
            $rank->package_id = $request->package_id;
            $rank->commission = $request->commission;
            $rank->rank_order = $request->priority;
            $rank->save();
            if ($request->has('image')) {
                $file = $request->file('image');
                $model = $rank;
                $prefix = 'rnk';
                if (! $this->singleFileUpload($file, $model, $prefix, 'ranks')) {
                    DB::rollback();
                    if ($request->ajax()) {
                        return response()->json([
                            'status' => false,
                            'message' => trans('rank.image_upload_failed'),
                        ], 400);
                    }

                    return redirect(route('rank'))->with('error', trans('rank.image_upload_failed'));
                }
            }
            if ($activeConfig->whereIn('slug', ['referral-count', 'personal-pv', 'group-pv', 'downline-member-count', 'joiner-package'])->isNotEmpty()) {
                $rankDetails->fill($validatedData);
                $rankDetails->rank()->associate($rank);
                $rankDetails->save();
            }

            if ($activeConfig->whereIn('slug', ['downline-rank-count'])->isNotEmpty()) {
                $downlineRankCount = collect($validatedData['downlineRank'])->filter(fn ($count) => $count > 0);
                $downlineRankCount->map(fn ($count, $rnk) => $rank->downlineRankCount()->attach($rnk, ['count' => $count]));
            }
            if ($activeConfig->whereIn('slug', ['downline-package-count'])->isNotEmpty()) {
                $downlineRankCount = collect($validatedData['packageId'])->filter(fn ($count) => $count > 0);
                $downlineRankCount->map(fn ($count, $pck) => $rank->downinePackCount()->attach($pck, ['count' => $count]));
            }
        } catch (\Throwable $th) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('rank.creation_error'),
                ], 400);
            }

            return redirect(route('rank'))->with('error', $th->getMessage());
        }
        DB::commit();
        if ($request->ajax()) {
            $rank->load('rankCriteria', 'downinePackCount', 'downlineRankCount');
            $currency = currencySymbol();
            $view = view('admin.settings.rank.inc.newRank', compact('activeConfig', 'rank', 'currency'))->render();

            return response()->json([
                'status' => true,
                'message' => trans('rank.creation_success'),
                'data' => $view,
            ]);
        }

        return redirect(route('rank'))->with('success', trans('rank.creation_success'));

        // TODO INSERT INTO USER ACTIVITY
        // TODO IF AUTH USER IS EMPLOYEE INSERT TO EMPLOYEE ACTIVITY
        // TODO INSERT INTO CONFIG CHANGE HISTORY
    }

    public function rankEdit($id)
    {
        $rank = Rank::with('downlineRankCount', 'downinePackCount', 'rankCriteria')->find($id);
        $edit = true;
        if ($rank->status) {
            $rankConfig = $this->rankConfig();
            $moduleStatus = $this->moduleStatus();
            $packages = $this->selectPackageRankConfig();
            $ranks = Rank::all();
            $activeConfig = (Cache::has('rankActiveConfig'))
                ? Cache::get('rankActiveConfig')
                : RankConfiguration::Active()->get();
            $currency = currencySymbol();
            $view = view('admin.settings.rank.inc.editRank', compact('rankConfig', 'activeConfig', 'packages', 'moduleStatus', 'ranks', 'edit', 'rank', 'currency'))->render();

            return response()->json([
                'status' => true,
                'data' => $view,
            ]);
        } else {
            return abort(403);
        }
    }

    public function rankUpdate(RankRequest $request, $rank)
    {
        if(session()->get('is_preset')){
            return response()->json([
            'status' => 'error',
            'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }
        $moduleStatus = $this->moduleStatus();
        $rank = Rank::findOrfail($rank);
        $validatedData = $request->validated();

        $currency = currencySymbol();
        $activeConfig = (Cache::has('rankActiveConfig'))
            ? Cache::get('rankActiveConfig')
            : RankConfiguration::Active()->get();

        DB::beginTransaction();
        try {
            $rankDetails = new RankDetail;
            unset($validatedData['image']);
            $validatedData['commission'] = defaultCurrency($validatedData['commission']);
            //$rank->fill($validatedData);
            $rank->name = $request->name;
            $rank->color = $request->color;
            $rank->package_id = $moduleStatus->ecom_status ? null : $request->package_id;
            $rank->oc_product_id = $moduleStatus->ecom_status ? $request->package_id : null;
            $rank->commission = $validatedData['commission'];
            $rank->save();

            if ($request->has('image')) {
                $file = $request->file('image');
                $model = $rank;
                $prefix = 'rnk';
                $folder = 'ranks';
                if (! $this->singleFileUpload($file, $model, $prefix, $folder)) {
                    DB::rollback();
                    if ($request->ajax()) {
                        return response()->json([
                            'status' => false,
                            'message' => trans('rank.upload_failed'),
                        ], 404);
                    }

                    return redirect(route('rank'))->with('error', trans('rank.upload_failed'));
                }
            }

            if ($activeConfig->whereIn('slug', ['referral-count', 'personal-pv', 'group-pv', 'downline-member-count'])->isNotEmpty()) {
                $rankDetails = $rank->load('rankCriteria')->rankCriteria;
                $rankDetails->fill($validatedData);
                $rankDetails->save();
            }

            if ($activeConfig->whereIn('slug', ['downline-rank-count'])->isNotEmpty()) {
                $downlineRankCount = collect($validatedData['downlineRank'])->filter(fn ($count) => $count > 0)->mapWithKeys(fn ($count, $rnk) => $da[] = [$rnk => ['count' => $count]])->toArray();
                $rank->downlineRankCount()->sync($downlineRankCount);
            }
            if ($activeConfig->whereIn('slug', ['downline-package-count'])->isNotEmpty()) {
                $downlineRankCount = collect($validatedData['packageId'])->filter(fn ($count) => $count > 0)->mapWithKeys(fn ($count, $pck) => $da[] = [$pck => ['count' => $count]])->toArray();
                $rank->downinePackCount()->sync($downlineRankCount);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('rank.updation_failed'),
                ], 400);
            }

            return redirect(route('rank'))->with('error', $th->getMessage());
        }
        DB::commit();
        if ($request->ajax()) {
            $ranks = Rank::with('rankCriteria', 'downinePackCount', 'downlineRankCount')->get();
            $view = view('admin.settings.rank.inc.body', compact('activeConfig', 'ranks', 'currency'))->render();

            return response()->json([
                'status' => true,
                'message' => trans('rank.update_success'),
                'data' => $view,
            ]);
        }

        return redirect()->back()->with('success', trans('rank.update_success'));

        // TODO INSERT INTO USER ACTIVITY
        // TODO IF AUTH USER IS EMPLOYEE INSERT TO EMPLOYEE ACTIVITY
        // TODO INSERT INTO CONFIG CHANGE HISTORY
    }

    public function rankStatusUpdate(Request $request, $id)
    {
        if(session()->get('is_preset')){
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }

        $rank = Rank::findOrfail($id);
        if ($rank->status == 'Active') {
            $rank->update([
                'status' => 'Disabled',
            ]);
        } elseif ($rank->status == 'Disabled') {
            $rank->update([
                'status' => 'Active',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => trans('rank.status_change'),
        ]);
    }
    public function unlinkImage(Request $request,$rank)
    {
        $rank = Rank::where('id',$rank)->first();
        if ($this->unlinkFile($rank)) {
            if ($request->ajax()) {
                $ranks = Rank::with('rankCriteria', 'downinePackCount', 'downlineRankCount')->get();
                $activeConfig = (Cache::has('rankActiveConfig'))
                    ? Cache::get('rankActiveConfig')
                    : RankConfiguration::Active()->get();
                $currency = currencySymbol();
                $view   = view('admin.settings.rank.inc.body', compact('activeConfig', 'ranks', 'currency'))->render();
                return response()->json([
                    'status' => true,
                    'message' => trans('rank.update_success'),
                    'data' => $view,
                ]);
            }

            return redirect(route('rank'))->with('error', trans('rank.image_upload_failed'));
        }
    }
}
