<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePlacementRequest;
use App\Http\Requests\SponsorChangeRequest;
use App\Models\StairStepConfig;
use App\Models\ToolTipConfig;
use App\Models\User;
use App\Models\OCProduct;
use App\Models\UploadImage;
use App\Services\TreeService;
use App\Services\UserApproveService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class TreeController extends CoreInfController
{
    protected $serviceClass;

    public function __construct(TreeService $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    public function treeView(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        if ($request->has('user') && $request->user != '') {
            $user = User::find($request->user);
        } else {
            $user = User::GetAdmin();
        }
        $data = $this->serviceClass->getTreeView($moduleStatus, $user->id);

        return view('network.treeview', compact('data', 'user'));
    }

    public function getChild(Request $request)
    {
        $user = User::findOrfail($request->id);
        $moduleStatus = $this->moduleStatus();
        $data = $this->serviceClass->getChild($moduleStatus, $user->id);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function genealogy(Request $request, $search = null)
    {
        $user       = User::GetAdmin();
        $level      = 0;
        $isNode     = 0;
        $isSearch   = false;
        $isNew      = false;
        if ($request->has('user') && $request->user != '') {
            $user = User::find($request->user) ?? User::GetAdmin();
            $moreusers = 0;
        }
        if ($request->has('level')) $level = $request->level;

        if ($request->has('isNode')) $isNode = $request->isNode;

        if ($request->has('isNew')) $isNew = $request->isNew;

        if ($request->has('search') && $request->search) {
            $isSearch = 1;
            $request->isMore = true;
        }

        $moduleStatus   = $this->moduleStatus();
        $configuration  = $this->configuration();
        $type           = "tree";
        $width          = $this->configuration()->tree_width;
        // dd($request->isNew);
        if ($request->has('isMore') && $request->isMore == 'true') {
            $treeData       = $this->serviceClass->gnealogyTreeDownline($user, $moduleStatus, $width, $type);
        } else {
            $treeData       = $this->serviceClass->gnealogyTree($user, $moduleStatus, $width, $isSearch, $type);
        }
        $renderTree     = $this->serviceClass->renderTree($treeData, $moduleStatus, $configuration, $level, 'tree', $isNode , $isNew);
        $prefix         = config('database.connections.mysql.prefix');
        $tooltipData    = Cache::get("{$prefix}tooltipData");
        $tooltipConfig  = ToolTipConfig::Active()->get();
        $treeIcon_based_on  = $this->configuration()->tree_icon_based;
        $memberStatus = UploadImage::all();
        if ($request->ajax()) {

            $tooltipView    = view('network.inc._tooltip', compact('tooltipData', 'tooltipConfig', 'moduleStatus', 'memberStatus'))->render();
            return response()->json([
                'status' => true,
                'data' => [
                    'tree' => $renderTree,
                    'tooltip' => $tooltipView,
                ],
            ]);
        }
        $activeTreeIcon = UploadImage::where('image_type', 'active_tree_icon')->first() ?? '';
        $inActiveTreeIcon = UploadImage::where('image_type', 'inactive_tree_icon')->first() ?? '';
        return view('network.genealogy', compact('renderTree', 'tooltipData', 'tooltipConfig', 'user', 'moduleStatus', 'treeIcon_based_on', 'activeTreeIcon', 'inActiveTreeIcon'));
    }

    public function downlineMembers(Request $request)
    {
        $user       = User::query();
        if ($request->has('user') && $request->user != '') {
            $user->where('users.id', $request->user);
        } else {
            $user->where('user_type', 'admin');
        }
        $user->withCount('closureChildren');

        $data   = $user->first();
        $level = DB::table('treepaths')
            ->join('users', 'users.id', 'descendant', 'users.id')
            ->where('ancestor', $data->id)
            ->selectRaw("COALESCE(MAX(user_level), 0) as level")
            ->first()->level;
        $data->descendants_max_user_level = $level;
        $moduleStatus = $this->moduleStatus();
        $levels     = $level;
        $level      = ($request->has('level') && $request->level != 'all')
            ? $data->user_level + $request->level
            : 0;

        if ($request->ajax()) {
            $dwonlines = $this->serviceClass->getDownlines($data, $moduleStatus, $level, $data);
            $dataTable = DataTables::of($dwonlines)
                ->addColumn('member', function (User $user) {
                    return '<div class="d-flex gap-5px"><img class="ht-30 img-transaction" src="' . asset('/assets/images/users/avatar-1.jpg') . '" style="max-width:30px;"><br><div class="transaction-user"><span class="downline-username">' . $user->username . '</span><h5><span class="downline-user-firstname">' . $user->userDetails->name . '</span></h5></div></div>';
                });
            if ($moduleStatus->mlm_plan == 'Binary' || $moduleStatus->mlm_plan == 'Matrix') {
                $dataTable->addColumn('placement', fn ($placement) => "<span>{$placement->fatherDetails->username}</span>");
            }

            return $dataTable->addColumn('sponsor', fn ($sponsor) => "<span>{$sponsor->sponsor->username}</span>")
                ->addColumn('level', fn ($level) => "<span>{$level->ref_level}</span>")
                ->addColumn('action', function ($user) {
                    $route = route('network.genealogy', ['user' => $user->id]);

                    return "<a href={$route} target='_blank'><i class='fas fa-sitemap'></i></a>";
                })
                ->rawColumns(['member', 'sponsor', 'level', 'action', 'placement'])
                ->make(true);
        }

        return view('network.downline-members', compact('data', 'moduleStatus'));
    }

    public function referralMembers(Request $request)
    {
        $user       = User::query();
        if ($request->has('user') && $request->user != '') {
            $user->where('users.id', $request->user);
        } else {
            $user->where('user_type', 'admin');
        }
        $user->withCount('closureSponsorChildren as sponsor_descendant_count');

        $data   = $user->first();
        $level = DB::table('sponsor_treepaths')
            ->join('users', 'users.id', 'descendant', 'users.id')
            ->where('ancestor', $data->id)
            ->selectRaw("COALESCE(MAX(user_level), 0) as level")
            ->first()->level;
        $data->sponsor_descendant_max_user_level = $level;
        $moduleStatus   = $this->moduleStatus();
        $levels         = $level;
        $level          = ($request->has('level') && $request->level != 'all')
            ? $data->user_level + $request->level
            : 0;

        if ($request->ajax()) {
            $dwonlines = $this->serviceClass->getRefferalDownlines($data, $moduleStatus, $level, $data);
            $dataTable = DataTables::of($dwonlines)
                ->addColumn('member', function (User $user) {
                    $fullName = ($user->userDetails()->exists()) ? $user->userDetails->name : ' ';

                    return '<div class="d-flex gap-5px"><img class="ht-30 img-transaction" src="' . asset('/assets/images/users/avatar-1.jpg') . '" style="max-width:30px;"><br><div class="transaction-user"><span class="downline-username">' . $user->username . '</span><h5><span class="downline-user-firstname">' . $fullName . '</span></h5></div></div>';
                });

            return $dataTable->addColumn('sponsor', fn ($sponsor) => "<span>{$sponsor->sponsor->username}</span>")
                ->addColumn('level', fn ($level) => "<span>{$level->ref_level}</span>")
                ->addColumn('joining_date', fn ($user) => '<span>' . Carbon::parse($user->date_of_joining)->format('d M Y h:iA') . '</span>')
                ->addColumn('action', function ($user) {
                    $route = route('network.sponsorTree', ['user' => $user->id]);

                    return "<a href={$route} target='_blank'><i class='fas fa-sitemap'></i></a>";
                })
                ->rawColumns(['member', 'sponsor', 'level', 'action', 'joining_date'])
                ->make(true);
        }
        return view('network.referral-members', compact('data'));
    }

    public function sponsorTree(Request $request)
    {
        $user = User::GetAdmin();
        $level = 0;
        $isSearch = false;
        $width = $this->configuration()->tree_width;
        if ($request->has('user') && $request->user != '') {
            $user = User::find($request->user) ?? User::GetAdmin();
        }
        if ($request->has('level')) {
            $level = $request->level;
        }

        if ($request->has('search') && $request->search) $isSearch = 1;

        $moduleStatus   = $this->moduleStatus();
        $configuration  = $this->configuration();
        $type           = 'sponsor_tree';
        $treeData       = $this->serviceClass->gnealogyTree($user, $moduleStatus, $width, $isSearch, $type);
        $renderTree     = $this->serviceClass->renderTree($treeData, $moduleStatus, $configuration, $level, $type);
        $prefix         = config('database.connections.mysql.prefix');
        $tooltipData    = Cache::get("{$prefix}tooltipData");
        $tooltipConfig  = ToolTipConfig::Active()->get();
        $treeIcon_based_on  = $this->configuration()->tree_icon_based;
        if ($request->ajax()) {
            $tooltipView    = view('network.inc._tooltip', compact('tooltipData', 'tooltipConfig', 'moduleStatus'))->render();
            return response()->json([
                'status'    => true,
                'data'      => [
                    'tree'  => $renderTree,
                    'tooltip' => $tooltipView
                ]
            ]);
        }
        $activeTreeIcon = UploadImage::where('image_type', 'active_tree_icon')->first() ?? null;
        $inActiveTreeIcon = UploadImage::where('image_type', 'inactive_tree_icon')->first() ?? null;
        return view('network.sponsor-tree', compact('renderTree', 'tooltipData', 'tooltipConfig', 'user', 'moduleStatus', 'treeIcon_based_on', 'activeTreeIcon', 'inActiveTreeIcon'));
    }

    public function changePlacement()
    {
        $moduleStatus = $this->moduleStatus();

        return view('network.change-placement', compact('moduleStatus'));
    }

    public function updatePlacement(ChangePlacementRequest $request)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $moduleStatus = $this->moduleStatus();
            if ($validatedData['user'] == $validatedData['placement'] && $moduleStatus->mlm_plan != 'Binary') {
                throw ValidationException::withMessages([
                    'placement' => __('tree.choose_another_placement'),
                ]);
            }
            $user = User::find($validatedData['user']);
            $placement = User::findorFail($validatedData['placement']);
            $checkPlacementIsPossible = $this->serviceClass->checkNewPlacementInTree($user, $placement, 'tree');
            if ($checkPlacementIsPossible) {
                throw ValidationException::withMessages([
                    'placement' => __('tree.cant_select_downline_as_placement'),
                ]);
            }
            $currentPosition = $user->position;
            $currentPlacement = $user->father_id;
            $newPlacement = $placement;
            $plan = $moduleStatus->mlm_plan;
            $newPosition = null;
            if ($plan == 'Binary') {
                $newPosition = $validatedData['position'];
                if ($currentPlacement == $placement->id && $currentPosition == $newPosition) {
                    throw ValidationException::withMessages([
                        'placement' => __('tree.choose_another_placement'),
                    ]);
                }
            }
            $checkPositionAvailable = $this->serviceClass->isPositionAvailable($plan, $newPosition, $placement);
            if (!$checkPositionAvailable) {
                throw ValidationException::withMessages([
                    'placement' => __('tree.choose_another_placement'),
                ]);
            }
            $changePosition = $this->serviceClass->changePosition($user, $currentPlacement, $currentPosition, $newPlacement, $newPosition, $plan);

            if ($plan == 'Unilevel') {
                $newSponsor = $newPlacement;
                $currentSponsor = User::find($user->sponsor_id);
                $this->serviceClass->changeSponsor($user, $newSponsor, $currentSponsor);
            }
            if ($changePosition) {
                DB::commit();

                return redirect()->back()->with('success', 'placement_changed_successfully');
            }
            DB::rollBack();

            return redirect()->back()->withErrors('placement_change_error');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function changeSponsor()
    {
        $moduleStatus = $this->moduleStatus();
        if (in_array($moduleStatus->mlm_plan, ['Stair_Step', 'Donation', 'Party'])) {
            abort(401);
        }

        return view('network.change-sponsor', compact('moduleStatus'));
    }

    public function updateSponsor(SponsorChangeRequest $request)
    {
        DB::beginTransaction();
        $moduleStatus = $this->moduleStatus();

        try {
            $validatedData = $request->validated();
            $user = User::find($validatedData['user']);
            $currentSponsor = User::find($user->sponsor_id);
            $newSponsor = User::find($validatedData['new_sponsor']);
            if (($validatedData['user'] == $validatedData['new_sponsor']) || ($user->sponsor_id == $newSponsor->id)) {
                throw ValidationException::withMessages([
                    'new_sponsor' => __('tree.choose_another_sponsor'),
                ]);
            }
            $checkNewSponsorIsPossible = $this->serviceClass->checkNewPlacementInTree($user, $newSponsor, 'sponsor_tree');
            if ($checkNewSponsorIsPossible) {
                throw ValidationException::withMessages([
                    'new_sponsor' => __('tree.cant_select_downline_as_sponsor'),
                ]);
            }

            $this->serviceClass->changeSponsor($user, $newSponsor, $currentSponsor);
            if ($moduleStatus->mlm_plan == 'Unilevel') {
                $newPlacement = $newSponsor;
                $currentPlacement = User::find($user->sponsor_id);
                $currentPosition = $user->position;
                $newPosition = '';
                $plan = $moduleStatus->mlm_plan;
                $changePosition = $this->serviceClass->changePosition($user, $currentPlacement, $currentPosition, $newPlacement, $newPosition, $plan);
            }
            DB::commit();

            return redirect()->back()->with('tree.sponsor_successfully_changed');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            throw $th;
        }
    }

    public function stepView(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $maxStep = StairStepConfig::orderBy('id', 'DESC')->get();
        $user = User::with('step')->withCount('referrals')->GetAdmin();
        if ($request->has('user') && $request->user != '') {
            $user = User::with('step')->withCount('referrals')->find($request->user);
        }
        $stepArray = collect([]);
        $tooltipArray = collect([]);
        $da['step'] = $user->step->step_id;
        $da['id'] = $user->user_id;
        $da['username'] = $user->username;
        $stepArray->push($da);

        $stepDataQry = User::query()
            ->join('stair_steps', 'stair_steps.user_id', '=', 'users.id')
            ->where('leader_id', $user->id)
            ->where('step_id', '!=', 0)
            ->where('step_id', '!=', null)
            ->where('breakaway_status', 0);
        if ($moduleStatus->rank_status) {
            $stepDataQry->with('rank');
        }

        $stepData = $stepDataQry->withCount('referrals')->get();

        $tooltipData = [
            'user_id' => $user->id,
            'user_name' => $user->username,
            'date_of_joining' => $user->date_of_joining,
            'user_photo' => '',
            'full_name' => $user->username,
            'referral_count' => $user->referrals_count,
            'group_pv' => $user->gpv,
            'personal_pv' => $user->pv,
        ];
        if ($moduleStatus->rank_status == 'yes') {
            // $tooltipArray[$key]["user_rank"] = $this->validation_model->getRankName($rank_id);
        } else {
            $tooltipData['user_rank'] = 'NA';
        }
        $tooltipArray->push($tooltipData);

        foreach ($stepData as $key => $value) {
            $da['step'] = $value->step_id;
            $da['id'] = $value->user_id;
            $da['username'] = $value->username;
            $stepArray->push($da);

            $tooltipData = [
                'user_id' => $value->user_id,
                'user_name' => $value->username,
                'date_of_joining' => $value->date_of_joining,
                'user_photo' => '',
                'full_name' => $value->username,
                'referral_count' => $value->referrals_count,
                'group_pv' => $value->gpv,
                'personal_pv' => $value->pv,
            ];
            if ($moduleStatus->rank_status == 'yes') {
                // $tooltipArray[$key]["user_rank"] = $this->validation_model->getRankName($rank_id);
            } else {
                $tooltipData['user_rank'] = 'NA';
            }
            $tooltipArray->push($tooltipData);
        }

        return view('network.step-view', compact('stepArray', 'maxStep', 'user', 'tooltipArray'));
    }

    public function collapseTree(Request $request)
    {
        $user = User::find($request->user);
        $treeWidth  = $this->configuration()->tree_width;;
        $isMore     = ($treeWidth <= $user->leg_position)
            ? "1"
            : "0";
        $tree = '<a href="javascript:void(0)" class="node-element"><img data-serialtip="example-' . $user->id . '" class="tooltipKey user-image" src="/assets/images/users/avatar-1.jpg" alt="Card image"><span class="tree-username ">' . $user->username . '</span></a><a href="javascript:void(0)" id="down-arrow-' . $user->id . '" data-level="0" onclick="getDownline(' . $user->id . ')" class="down-arrow" data-ismore="' . $isMore . '"><img src="/assets/images/down.png" class="img-down" data-level="0"  data-ismore="' . $isMore . '"></a>';
        $prefix         = config('database.connections.mysql.prefix');
        $moduleStatus   = $this->moduleStatus();
        $tooltipData    = Cache::get("{$prefix}tooltipData");
        $tooltipConfig  = ToolTipConfig::Active()->get();
        $treeIcon_based_on  = $this->configuration()->tree_icon_based;
        $memberStatus = UploadImage::all();
        $tooltipView    = view('network.inc._tooltip', compact('tooltipData', 'tooltipConfig', 'moduleStatus', 'memberStatus'))->render();


        return response()->json([
            'status' => true,
            'data' => $tree,
            'tootip' => $tooltipView
        ]);
    }
    public function moreChildren(int $father = null, int $count)
    {
        $moduleStatus       = $this->moduleStatus();
        $treeIcon_based_on  = $this->configuration()->tree_icon_based;
        $totalChildren      = User::where('id', $father)->withCount('children')->first();
        $children = User::select('users.id', 'users.username', 'users.position', 'users.user_rank_id', 'users.active', 'users.date_of_joining', 'users.father_id')
            ->where('father_id', $father)->where('leg_position', '>', $count)
            ->orderBy('leg_position')
            ->when($moduleStatus['rank_status'], fn ($query) => $query->with('rankDetail'))
            ->when($moduleStatus['mlm_plan'] == 'Binary', fn ($query) => $query->with('legDetails'))
            ->when($moduleStatus['mlm_plan'] == 'Stair_Step', fn ($query) => $query->with('pvDetails'), fn ($qry) => $qry->addselect("users.personal_pv", "users.group_pv as gpv", "users.product_id", "users.oc_product_id"))
            ->when($moduleStatus['mlm_plan'] == 'Donation', fn ($query) => $query->addSelect('donation_rates.name as donationLevel')
                ->leftJoin('donation_levels', 'users.id', '=', 'donation_levels.user')
                ->leftJoin('donation_rates', 'donation_rates.id', '=', 'donation_levels.level')->groupBy('donationLevel'))
            ->with(['fatherDetails', 'package', 'userDetails'])
            ->withCount(['children', 'package'])
            ->groupBy('users.id')
            ->limit(3)
            ->get();
        $balanceChild   = $totalChildren->children_count - ($children->count() + $count);
        if (!$children->isEmpty()) {
            $tooltipConfig      = ToolTipConfig::Active()->get();
            $treeIcon_based_on  = $this->configuration()->tree_icon_based;
            $memberStatus       = UploadImage::all();
            $tooltipData        = $children;
            $tooltipView        = view('network.inc._tooltip', compact('tooltipData', 'tooltipConfig', 'moduleStatus', 'memberStatus'))->render();
            $data               = $children->map(function ($user) use ($moduleStatus, $treeIcon_based_on) {
                $out['user_id'] = $user->id;
                if ($moduleStatus->ecom_status) {
                    $ocProductId                = User::where('id', $user->id)->first();
                    $membership_package         = OCProduct::where('status', 1)->where('package_type', 'registration')->where('product_id', $ocProductId->oc_product_id)->first();
                } else {
                    $membership_package         =   $user->package;
                }
                if ($treeIcon_based_on == "member_pack") {
                    $userImage      = ($membership_package->tree_icon)
                        ? $membership_package->tree_icon
                        : "/assets/images/users/avatar-1.jpg";
                } elseif ($treeIcon_based_on == 'profile_image') {
                    $userImage = ($user->userDetail->image) ? $user->userDetail->image : "/assets/images/users/avatar-1.jpg";
                } elseif ($treeIcon_based_on == 'rank') {
                    $userImage = $user->rankDetail->tree_icon ?? "/assets/images/users/avatar-1.jpg";
                } elseif ($treeIcon_based_on == 'member_status') {
                    $userImage = $this->serviceClass->memberStatus($user->active) ?? "/assets/images/users/avatar-1.jpg";
                } else {
                    $userImage = "/assets/images/users/avatar-1.jpg";
                }
                $blockedLabel = ($user->active == 0) ? 'bg-danger' : '';
                $out['tree'] =  "<a href='javascript:void(0)'  class='node-element'><img data-serialtip='example-{$user->id}' class='tooltipKey user-image' src='" . asset($userImage) . "' alt='Card image'><span class='tree-username {$blockedLabel}'>{$user->username}</span><a href='javascript:void(0)' id='down-arrow-{$user->id}' data-ismore='1' onclick='getDownline({$user->id})' class='down-arrow' ><img src='/assets/images/down.png' data-level='0' data-ismore='1' class='img-down' id='img-down-{$user->id}'></a></a>";
                return $out;
            });
            return response()->json([
                'status'    => true,
                'data'      => [
                    'children' => $data,
                    'tooltipData' => $tooltipView,
                    'more' => $balanceChild
                ]
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'data'      => [],
            ], 404);
        }
    }

    public function reEntry(Request $request)
    {
        $user      = ($request->has('user') && $request->user != '')
                        ? User::with('reentries.sponsor', 'reentries.reentryParent.parentDetail')->withCount('reentries')->find($request->user)
                        : User::with('reentries.reentryParent.parentDetail')->withCount('reentries')->GetAdmin();
        return view('network.re-entry', compact('user'));
    }

    public function moreSponsorChildren(int $sponsor = null, int $count)
    {
        $moduleStatus       = $this->moduleStatus();
        $treeIcon_based_on  = $this->configuration()->tree_icon_based;
        $totalChildren      = User::where('id', $sponsor)->withCount('referrals')->first();
        $children = User::select('users.id', 'users.username', 'users.position', 'users.user_rank_id', 'users.active', 'users.date_of_joining', 'users.sponsor_id')
            ->where('sponsor_id', $sponsor)->where('sponsor_index', '>', $count)
            ->orderBy('sponsor_index')
            ->when($moduleStatus['rank_status'], fn ($query) => $query->with('rankDetail'))
            ->when($moduleStatus['mlm_plan'] == 'Binary', fn ($query) => $query->with('legDetails'))
            ->when($moduleStatus['mlm_plan'] == 'Stair_Step', fn ($query) => $query->with('pvDetails'), fn ($qry) => $qry->addselect("users.personal_pv", "users.group_pv as gpv", "users.product_id", "users.oc_product_id"))
            ->when($moduleStatus['mlm_plan'] == 'Donation', fn ($query) => $query->addSelect('donation_rates.name as donationLevel')
                ->leftJoin('donation_levels', 'users.id', '=', 'donation_levels.user')
                ->leftJoin('donation_rates', 'donation_rates.id', '=', 'donation_levels.level')->groupBy('donationLevel'))
            ->with(['sponsor', 'package', 'userDetails'])
            ->withCount(['referrals', 'package'])
            ->groupBy('users.id')
            ->limit(3)
            ->get();
        $balanceChild   = $totalChildren->referrals_count - ($children->count() + $count);
        if (!$children->isEmpty()) {
            $tooltipConfig      = ToolTipConfig::Active()->get();
            $treeIcon_based_on  = $this->configuration()->tree_icon_based;
            $memberStatus       = UploadImage::all();
            $tooltipData        = $children;
            $tooltipView        = view('network.inc._tooltip', compact('tooltipData', 'tooltipConfig', 'moduleStatus', 'memberStatus'))->render();
            $data               = $children->map(function ($user) use ($moduleStatus, $treeIcon_based_on) {
                $out['user_id'] = $user->id;
                if ($moduleStatus->ecom_status) {
                    $ocProductId                = User::where('id', $user->id)->first();
                    $membership_package         = OCProduct::where('status', 1)->where('package_type', 'registration')->where('product_id', $ocProductId->oc_product_id)->first();
                } else {
                    $membership_package         =   $user->package;
                }
                if ($treeIcon_based_on == "member_pack") {
                    $userImage      = ($membership_package->tree_icon)
                        ? $membership_package->tree_icon
                        : "/assets/images/users/avatar-1.jpg";
                } elseif ($treeIcon_based_on == 'profile_image') {
                    $userImage = ($user->userDetail->image) ? $user->userDetail->image : "/assets/images/users/avatar-1.jpg";
                } elseif ($treeIcon_based_on == 'rank') {
                    $userImage = $user->rankDetail->tree_icon ?? "/assets/images/users/avatar-1.jpg";
                } elseif ($treeIcon_based_on == 'member_status') {
                    $userImage = $this->serviceClass->memberStatus($user->active) ?? "/assets/images/users/avatar-1.jpg";
                } else {
                    $userImage = "/assets/images/users/avatar-1.jpg";
                }
                $blockedLabel = ($user->active == 0) ? 'bg-danger' : '';
                $out['tree'] =  "<a href='javascript:void(0)'  class='node-element'><img data-serialtip='example-{$user->id}' class='tooltipKey user-image' src='" . asset($userImage) . "' alt='Card image'><span class='tree-username {$blockedLabel}'>{$user->username}</span><a href='javascript:void(0)' id='down-arrow-{$user->id}' data-ismore='1' onclick='getDownline({$user->id})' class='down-arrow' ><img src='/assets/images/down.png' data-level='0' data-ismore='1' class='img-down' id='img-down-{$user->id}'></a></a>";
                return $out;
            });
            return response()->json([
                'status'    => true,
                'data'      => [
                    'children' => $data,
                    'tooltipData' => $tooltipView,
                    'more' => $balanceChild
                ]
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'data'      => [],
            ], 404);
        }
    }
}
