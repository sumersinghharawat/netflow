<?php

namespace App\Services;

use App\Models\User;
use App\Models\Package;
use App\Models\Treepath;
use App\Models\OCProduct;
use App\Models\UploadImage;
use App\Models\ModuleStatus;
use App\Models\SponsorTreepath;
use Illuminate\Support\Facades\DB;
use App\Models\SponsorChangeHistory;
use Illuminate\Support\Facades\Cache;
use App\Models\PlacementChangeHistory;
use App\Http\Controllers\CoreInfController;
use App\Models\MonolineConfig;

class TreeService
{
    public $graph = '';

    public $currentLevel;

    public $cacheNodeRight = [];

    public function getTreeView($moduleStatus, $user)
    {
        $nodeQuery = User::query();
        $nodeData = $nodeQuery->with('children.userDetails')
            ->where('id', $user);
        if ($moduleStatus->mlm_plan == 'Binary') {
            $nodeData->with('children.legDetails');
        }
        if ($moduleStatus->rank_status) {
            $nodeData->with('children.rankDetail');
        }

        $nodeDataResult = $nodeData->first();
        $data = [];
        $tooltipData = [];
        foreach ($nodeDataResult->children->sortBy('leg_position') as $key => $children) {
            $data[$key]['title'] = $children->username;
            $data[$key]['full_name'] = $children->userDetails->name . ' ' . $children->userDetails->second_name;
            $data[$key]['id'] = $children->id;
            $data[$key]['level'] = $children->user_level;
            $data[$key]['child'] = ($children->loadCount('children')->children_count == 0) ? false : true;
            $data[$key]['image'] = $children->userDetails->image;
            $tooltipData[$key] = [
                'img' => $children->userDetails->image ?? false,
                'user_id' => $children->id,
                'username' => $children->username,
                'full_name' => $children->userDetails->name . ' ' . $children->userDetails->second_name,
                'join_date' => $children->date_of_joining,
                'personal_pv' => $children->personal_pv,
                'gpv' => $children->group_pv,
            ];
            if ($moduleStatus->mlm_plan == 'Binary') {
                $tooltipData[$key]['left'] = $children->legDetails->total_left_count;
                $tooltipData[$key]['right'] = $children->legDetails->total_right_count;
                $tooltipData[$key]['left_carry'] = $children->legDetails->total_left_carry;
                $tooltipData[$key]['right_carry'] = $children->legDetails->total_right_carry;
            }
            if ($moduleStatus->rank_status && $children->rankDetail()->exists()) {
                $tooltipData[$key]['rank'] = $children->rankDetail;
            }
        }
        $view = view('network.tree-node', compact('data'))->render();

        return [
            'view' => $view,
            'tooltip' => json_encode($tooltipData),
        ];
    }

    public function getChild($moduleStatus, $user = null)
    {
        $nodeQuery = User::query();
        $nodeData = $nodeQuery->with('children.userDetails')
            ->where('id', $user);
        if ($moduleStatus->mlm_plan == 'Binary') {
            $nodeData->with('children.legDetails');
        }
        if ($moduleStatus->rank_status) {
            $nodeData->with('children.rankDetail');
        }

        $nodeDataResult = $nodeData->first();
        $data = [];
        $tooltipData = [];
        $key = 0;
        foreach ($nodeDataResult->children->sortBy('leg_position') as $children) {
            $data[$key]['title'] = $children->username;
            $data[$key]['full_name'] = $children->userDetails->name . ' ' . $children->userDetails->second_name;
            $data[$key]['id'] = $children->id;
            $data[$key]['level'] = $children->user_level;
            $data[$key]['child'] = ($children->loadCount('children')->children_count == 0) ? false : true;
            $data[$key]['image'] = $children->userDetails->image;
            $tooltipData[$key] = [
                'img' => $children->userDetails->image ?? false,
                'user_id' => $children->id,
                'username' => $children->username,
                'full_name' => $children->userDetails->name . ' ' . $children->userDetails->second_name,
                'join_date' => $children->date_of_joining,
                'personal_pv' => $children->personal_pv,
                'gpv' => $children->group_pv,
            ];
            if ($moduleStatus->mlm_plan == 'Binary') {
                $tooltipData[$key]['left'] = $children->legDetails->total_left_count;
                $tooltipData[$key]['right'] = $children->legDetails->total_right_count;
                $tooltipData[$key]['left_carry'] = $children->legDetails->total_left_carry;
                $tooltipData[$key]['right_carry'] = $children->legDetails->total_right_carry;
            }
            if ($moduleStatus->rank_status && $children->rankDetail) {
                $tooltipData[$key]['rank'] = $children->rankDetail;
            }
            $key++;
        }
        $view = view('network.tree-child', compact('data'))->render();
        return [
            'view' => $view,
            'tooltip' => json_encode($tooltipData),
        ];
    }

    public function gnealogyTree(User $user, $moduleStatus, $width, $isSearch, $type = 'tree')
    {
        $coreController = new CoreInfController;
        $treeDepth  = $coreController->configuration()->tree_depth;
        $prefix     = config('database.connections.mysql.prefix');
        $treeQry    = User::query();
        $da         = $treeQry->with(['package','userDetails'])
                        ->when($moduleStatus['rank_status'], fn($query) => $query->with('rankDetail'))
                        ->when($moduleStatus['mlm_plan'] == 'Binary', fn($query) => $query->with('legDetails'))
                        ->when($moduleStatus['mlm_plan'] == 'Stair_Step', fn($query) => $query->with('pvDetails'), fn($qry) => $qry->select("users.personal_pv", "users.group_pv as gpv", "users.product_id", "users.oc_product_id"))
                        ->when($moduleStatus['mlm_plan'] == 'Donation', fn($query) => $query->addSelect('donation_rates.name as donationLevel')
                            ->leftJoin('donation_levels', 'users.id', '=', 'donation_levels.user')
                            ->leftJoin('donation_rates', 'donation_rates.id', '=', 'donation_levels.level')->groupBy('donationLevel'))
                        ->when($type == 'tree', fn($query) => $query->selectRaw("{$prefix}users.id, {$prefix}users.user_type, {$prefix}users.username,{$prefix}users.position,{$prefix}users.user_rank_id, {$prefix}users.active, {$prefix}users.date_of_joining, {$prefix}users.father_id,GROUP_CONCAT(DISTINCT CONCAT({$prefix}uplines.user_level - {$user->user_level}, LPAD({$prefix}uplines.leg_position, 8 , 0)) ORDER BY {$prefix}uplines.user_level SEPARATOR '-') as tree_rank")
                            ->when($moduleStatus['mlm_plan'] == "Unilevel", fn($qry) => $qry->addSelect(DB::raw("MAX({$prefix}uplines.leg_position) as leg_position_rank")))
                            ->selectRaw("{$prefix}treepath1.depth as depth")
                            ->with('fatherDetails')
                            ->join('treepaths as treepath1', 'treepath1.descendant', '=', 'users.id')
                            ->join('treepaths as treepath2', 'treepath2.descendant', '=', 'treepath1.descendant')
                            ->whereRaw("{$prefix}users.user_level - {$user->user_level} < {$treeDepth}")
                            ->where('treepath1.ancestor', $user->id)
                            ->join('users AS uplines', fn($join) => $join->on('uplines.id', '=', 'treepath2.ancestor')->where('uplines.user_level', '>=', $user->user_level))
                        )
                        ->when($type == 'sponsor_tree', fn($query) => $query->selectRaw("{$prefix}users.id, {$prefix}users.username, {$prefix}users.position, {$prefix}users.active, {$prefix}users.date_of_joining, {$prefix}users.sponsor_id,GROUP_CONCAT(DISTINCT CONCAT({$prefix}uplines.sponsor_level - {$user->sponsor_level}, LPAD({$prefix}uplines.leg_position, 8, 0), {$prefix}treepath2.ancestor ) ORDER BY {$prefix}uplines.sponsor_level SEPARATOR '-') as tree_rank, COALESCE(MAX({$prefix}uplinesSponsor.sponsor_index), 0) as sp_index")
                            ->selectRaw("{$prefix}treepath1.depth  depth")
                            ->with('sponsor')
                            ->join('sponsor_treepaths as treepath1', 'treepath1.descendant', 'users.id')
                            ->join('sponsor_treepaths as treepath2', 'treepath2.descendant', '=', 'treepath1.descendant')
                            ->whereRaw("({$prefix}users.sponsor_level - {$user->sponsor_level}) < {$treeDepth}")
                            ->where('treepath1.ancestor', $user->id)
                            ->join('users AS uplines', fn($join) => $join->on('uplines.id', '=', 'treepath2.ancestor')
                            ->where('uplines.sponsor_level', '>=', $user->sponsor_level))
                            ->leftJoin('users AS uplinesSponsor', fn($join) => $join->on('uplinesSponsor.id', '=', 'uplines.id')->where('uplinesSponsor.id', '!=', $user->id))
                            ->having('sp_index', '<=', $width)
                            )
                        ->withCount(['children', 'package', 'referrals'])
                        ->groupBy('users.id')
                        ->whereIn('users.user_type', ['user', 'admin'])
                        ->where('treepath1.depth', '<', $treeDepth)
                        ->when($moduleStatus['mlm_plan'] === 'Unilevel' && !$isSearch, fn ($query) => $query->having('leg_position_rank', '<=', $width))
                        ->orderBy('tree_rank')
                        ->get();
                        // dd($da);
        return $da;
    }

    public function renderTree($treeData, $moduleStatus, $configuration, $downLevel, $type = 'tree', $isNode = 0 , $isNew = 0)
    {
        $coreController = new CoreInfController;
        $treeDepth  = $coreController->configuration()->tree_depth;
        $treeWidth  = $coreController->configuration()->tree_width;
        if(!$isNew){
            $this->graph = "<ul class='node head node-head' id='node' data-isNode={$isNode}>";
        }
        $this->currentLevel = -1;
        $prefix = config('database.connections.mysql.prefix');
        Cache::forever("{$prefix}tooltipData", $treeData);
        while (!$treeData->isEmpty()) {
            $user = $treeData->shift();
            $level = $user->depth ?? 0;
            $checkEmpty = $treeData->isEmpty();
            if (auth()->user()->user_type == 'employee') {
                $authUser = User::GetAdmin();
            } else {
                $authUser = auth()->user();
            }
            $this->renderTreeNode($user, $authUser, $type, $downLevel , $isNew);
            $this->currentLevel = $user->depth ?? 0;
            $nodes = [];
            if ($type == 'tree') {
                $level = ($user->depth ?? 0) + 1;
                $childrenCount = ($level < $treeDepth) ? $user->children_count : 0;
                $placement = $user->username;
                if ($moduleStatus->mlm_plan == 'Binary' && $childrenCount < 2) {
                    $childPosition = ($childrenCount == 0) ? 'R' : $user->children[0]->position;
                    $left_node_disabled = $right_node_disabled = false;
                    if ($childrenCount == 0 || ($childrenCount == 1 && $childPosition == 'R')) {
                        $nodeUrl = route('register.form', [$placement, 'L']);
                        $nodes[] = [
                            'position' => 'L',
                            'level' => $level,
                            'nodeUrl' => $nodeUrl,
                            'disabled' => $left_node_disabled,
                        ];
                    }
                    if ($childrenCount == 0) {
                        $nodeUrl = route('register.form', [$placement, 'R']);
                        $nodes[] = [
                            'position' => 'R',
                            'level' => $level,
                            'nodeUrl' => $nodeUrl,
                            'disabled' => $right_node_disabled,
                        ];
                    }
                    if ($childrenCount == 1 && $childPosition == 'L') {
                        $nodeUrl = route('register.form', [$placement, 'R']);

                        $this->cacheNodeRight[] = [
                            'position' => 'R',
                            'level' => $level,
                            'nodeUrl' => $nodeUrl,
                            'disabled' => $right_node_disabled,
                            'childrenCount' => $childrenCount,
                            'father' => $user->id,
                            'username' => $user->username
                        ];
                    }
                } elseif (($moduleStatus->mlm_plan == 'Matrix' && $childrenCount < $configuration->width_ceiling) || ($moduleStatus->mlm_plan != 'Binary' && $moduleStatus->mlm_plan != 'Matrix')) {
                    $nodeDisabled = false;
                    $nodeUrl = route('register.form', [$placement, $childrenCount + 1]);
                    if ($moduleStatus->mlm_plan == 'Unilevel' && $authUser->id != $user->id && auth()->user()->user_type != 'admin') {
                        $nodeDisabled = true;
                        $nodeUrl = 'javascript:void(0);';
                    }
                    if ($childrenCount == 0) {
                        $nodes[] = [
                            'position' => $childrenCount + 1,
                            'level' => $level,
                            'nodeUrl' => $nodeUrl,
                            'disabled' => $nodeDisabled,
                            'childrenCount' => $childrenCount
                        ];
                    }
                    if ($childrenCount > 0) {
                        $this->cacheNodeRight[] = [
                            'position' => $childrenCount + 1,
                            'level' => $level,
                            'nodeUrl' => $nodeUrl,
                            'disabled' => $nodeDisabled,
                            'childrenCount' => $childrenCount,
                            'father' => $user->id,
                            'username' => $user->username

                        ];
                    }
                }

                $this->renderTempNode($nodes, $type);

                if ($checkEmpty) {
                    foreach (array_reverse($this->cacheNodeRight) as $node) {
                        $this->renderTempNode([$node], $type);
                        array_pop($this->cacheNodeRight);
                    }
                }
            } else {
                $level = ($user->depth ?? 0) + 1;
                $childrenCount = ($level < $treeDepth) ? $user->referrals_count : 0;
                if($childrenCount >= $treeWidth) {
                    $this->cacheNodeRight[] = [
                        'position'      => $user->referrals_count +1,
                        'username'      => $user->username,
                        'level'         => $level,
                        'childrenCount' => $childrenCount,
                        'father'        => $user->id,
                        'disabled'      => false,
                        'nodeUrl'       => ''
                    ];
                }
                if ($checkEmpty) {
                    foreach (array_reverse($this->cacheNodeRight) as $node) {
                        $this->renderSponsorTreeTempNode($node, $type);
                        array_pop($this->cacheNodeRight);
                    }
                }
            }
            if ($checkEmpty) {
                $this->graph .= str_repeat('</li></ul>', $this->currentLevel);
                $this->graph .= '</li>';
            }
        }

        $this->graph .= '</ul>';
        return $this->graph;
    }

    protected function renderTreeNode($user, $logedInUser, $type, $downLevel , $isNew)
    {
        $coreController = new CoreInfController;
        $treeDepth  = $coreController->configuration()->tree_depth;
        $moduleStatus = $coreController->moduleStatus();
        foreach (array_reverse($this->cacheNodeRight) as $node) {
            if ($node['level'] > $user->depth) {
                if($type == 'tree') {
                    $this->renderTempNode([$node], $type);
                } else {
                    $this->renderSponsorTreeTempNode($node, $type);
                }
                array_pop($this->cacheNodeRight);
            } else {
                break;
            }
        }
        $level          = $user->depth;
        $upIconFlag     = ($level < 1) && ($user->id != $logedInUser->id);
        $downIconFlag   = ($level + 1 >= $treeDepth);
        if ($upIconFlag) {
            $parentId = ($type == 'tree') ? $user->fatherDetails->id : $user->sponsor->id;
        }

        if ($level == $this->currentLevel) {
            $this->graph .= '</li>';
        }
        if ($level > $this->currentLevel && $level) {
            $this->graph .= '<ul>';
        }
        if ($level < $this->currentLevel) {
            $this->graph .= str_repeat('</li></ul>', $this->currentLevel - $level);
        }
        $this->graph .= "<li id='node-id-{$user->id}'>";
        if ($upIconFlag) {
            $this->graph .= "<a href='javascript:void(0)' data-parent='{$parentId}' onclick='collapseTree({$user->id}, this)' id='up-arrow-{$user->id}' class='up-arrow'><img  src='/assets/images/up.png' class='img-down'></a>";
        }
        if ($moduleStatus->mlm_plan == 'Monoline' && $user->user_type == 'reentry') {
            $userImage = MonolineConfig::first()->tree_icon ?? "/assets/images/users/avatar-1.jpg";
        } else {
            $treeIcon_based_on  = $coreController->configuration()->tree_icon_based;
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
                $userImage = $this->memberStatus($user->active) ?? "/assets/images/users/avatar-1.jpg";
            } else {
                $userImage = "/assets/images/users/avatar-1.jpg";
            }
        }

        $blockedLabel = ($user->active == 0) ? 'bg-danger' : '';
        $this->graph .= "<a href='javascript:void(0)' class='node-element'><img ondblclick='searchUser({$user->id})' data-serialtip='example-{$user->id}' id='node-img-{$user->id}' class='tooltipKey user-image' src='" . asset($userImage) . "' alt='Card image'><span class='tree-username {$blockedLabel}'>{$user->username}</span></a>";
        if ($downIconFlag) {
            $this->graph .= "<a href='javascript:void(0)' id='down-arrow-{$user->id}' data-level='{$downLevel}' onclick='getDownline({$user->id})' class='down-arrow' ><img src='/assets/images/down.png' class='img-down' data-level='{$downLevel}' id='img-down-{$user->id}'></a>";
        }
    }

    public function memberStatus($status)
    {
        if ($status == 1) {
            $uploadImage = UploadImage::where('image_type', 'active_tree_icon')->first();
            if ($uploadImage) {
                $userImage = 'storage/' . $uploadImage->image ?? "/assets/images/users/avatar-1.jpg";
            } else {
                $userImage = "/assets/images/users/avatar-1.jpg";
            }
            return $userImage;
        }
        $uploadImage = UploadImage::where('image_type', 'inactive_tree_icon')->first();
        if ($uploadImage) {
            $userImage = 'storage/' . $uploadImage->image ?? "/assets/images/users/avatar-1.jpg";
        } else {
            $userImage = "/assets/images/users/avatar-1.jpg";
        }
        return $userImage;
    }

    public function renderTempNode($nodes, $type)
    {
        $coreInfController = new CoreInfController;
        $treeDepth         = $coreInfController->configuration()->tree_depth;
        $treeWidth         = $coreInfController->configuration()->tree_width;
        $moduleStatus       = $coreInfController->moduleStatus();

        foreach ($nodes as $k => $node) {
            if ($node['level'] < $treeDepth) {
                // $node_icon = $node['disabled'] ? $assets['temp_icon_inactive'] : $assets['temp_icon'];
                if ($node['level'] == $this->currentLevel) {
                    $this->graph .= '</li>';
                }
                if ($node['level'] > $this->currentLevel) {
                    $this->graph .= '<ul>';
                }
                if ($node['level'] < $this->currentLevel) {
                    $this->graph .= str_repeat('</li></ul>', $this->currentLevel - $node['level']);
                }
                if ($moduleStatus->mlm_plan != 'Monoline' && $moduleStatus->mlm_plan != 'Unilevel') {
                    $this->graph .= '<li>';
                }
                if ($moduleStatus->mlm_plan == 'Unilevel') {
                    if ($node['childrenCount'] >= $treeWidth) {
                        $more = $node['childrenCount'] - $treeWidth;
                        if ($more) {
                            $position = $node['position'] - 1;
                            $this->graph .= "<li class='more-btn' id='more-children-li-{$node['father']}'><a href='javascript:void(0)' id='more-{$node['father']}'><img class='more-image' width='60' src='/assets/images/users/avatar-1.jpg' alt='Card image'><span class='label more-btn' id='more-child-{$node['father']}' data-username='{$node['username']}' data-clicked='false' onClick='getsiblings({$node['father']}, {$position})'><strong>{$more}</strong> More ></span></a></li>";
                        }
                    }
                    $this->graph .= '<li class="add-btn">';
                }
                if ($node['position'] === 1 && $moduleStatus->mlm_plan == 'Monoline') {
                    $this->graph .= '<a href="' . $node['nodeUrl'] . '" target="_blank" ><img class="add-image" src="/assets/images/add.png" alt="Card image"><span class="label add-btn">Add</span></a>';
                }
                if (!$node['disabled'] && $moduleStatus->mlm_plan != 'Monoline') {
                    $this->graph .= '<a href="' . $node['nodeUrl'] . '" target="_blank" ><img class="add-image" src="/assets/images/add.png" alt="Card image"><span class="label add-btn">Add</span></a>';
                }

                $this->currentLevel = $node['level'];
            }
        }
    }

    public function getDownlines($data, $moduleStatus, $level, $user)
    {
        $qry = User::query();
        $prefix = config('database.connections.mysql.prefix');
        $qry->join('treepaths', 'treepaths.descendant', 'users.id')
            ->where('treepaths.ancestor', $data->id)
            ->whereColumn('ancestor', '!=', 'descendant');
        if ($moduleStatus->mlm_plan == 'Binary' || $moduleStatus->mlm_plan == 'Matrix') {
            $qry->with('fatherDetails');
        }
        if ($level != 0) {
            $qry->where('users.user_level', $level);
        }
        $qry->selectRaw("{$prefix}users.*, {$prefix}users.user_level -  {$user->user_level}   as ref_level");

        return $qry->with('sponsor', 'userDetails');
    }

    public function getRefferalDownlines($data, $moduleStatus, $level, $user)
    {
        $prefix = config('database.connections.mysql.prefix');
        $qry = User::query();
        $qry->join('sponsor_treepaths', 'sponsor_treepaths.descendant', 'users.id')
            ->where('sponsor_treepaths.ancestor', $user->id)
            ->whereColumn('ancestor', '!=', 'descendant');
        if ($moduleStatus->mlm_plan == 'Binary' || $moduleStatus->mlm_plan == 'Matrix') {
            $qry->with('fatherDetails');
        }
        if ($level != 0) {
            $qry->where('sponsor_level', $level);
        }
        $qry->selectRaw("{$prefix}users.*, {$prefix}users.sponsor_level -{$data->sponsor_level}  as ref_level");

        return $qry->with('sponsor:id,username', 'userDetails');
    }

    public function checkNewPlacementInTree($user, $placement, $treeType = 'tree')
    {
        if ($treeType == 'tree') {
            $query = Treepath::query();
        } elseif ($treeType == 'sponsor_tree') {
            $query = SponsorTreepath::query();
        }
        $count = $query->where('ancestor', $user->id)
            ->where('descendant', $placement->id)
            ->count();

        return $count > 0;
    }

    public function isPositionAvailable($plan, $position, $placement)
    {
        $query = User::query();
        $query->where('father_id', $placement->id);
        if ($plan == 'Binary') {
            $check = $query->where('position', $position)
                ->count();

            return !$check;
        } elseif ($plan == 'Matrix') {
            $coreController = new CoreInfController;
            $width = $coreController->configuration()->width_ceiling;
            $check = $query->count();

            return $check < $width;
        } else {
            return true;
        }
    }

    public function changePosition($user, $currentPlacement, $currentPosition, $newPlacement, $newPosition, $plan)
    {
        $downlines = Treepath::where('ancestor', $user->id)
            ->select('descendant')
            ->get()->map(fn ($downline) => $downline->descendant)->toArray();
        $ancestors = Treepath::where('descendant', $user->id)
            ->where('ancestor', '!=', $user->id)
            ->select('ancestor')->get()->map(fn ($ancestor) => $ancestor->ancestor)->toArray();

        $this->deleteUpDowns($downlines, $ancestors);

        $newPlacementAncestors = Treepath::where('descendant', $newPlacement->id)
            ->select('ancestor')
            ->get()->map(fn ($newUp) => $newUp->ancestor);
        $newTreePathData = [];

        foreach ($downlines as $descendant) {
            foreach ($newPlacementAncestors as $ancestor) {
                $created_at = $updated_at = now();
                $newTreePathData[] = compact('ancestor', 'descendant', 'created_at', 'updated_at');
            }
        }
        Treepath::insert($newTreePathData);

        // update user level
        $currentDepth = User::find($currentPlacement)->user_level ?? null;
        $placementDepth = $newPlacement->user_level ?? null;
        $newLevel = (int) $placementDepth - (int) $currentDepth;

        if ($newLevel != 0) {
            User::whereIn('id', $downlines)
                ->increment('user_level', $newLevel);
        }

        if ($plan == 'Binary') {
            $legPosition = ($newPosition == 'R') ? 2 : 1;
        } else {
            $totalDownlineCount = User::where('father_id', $newPlacement->id)->count();
            $newPosition = $legPosition = $totalDownlineCount + 1;
        }

        $this->changeFatherAndPosition($user, $currentPlacement, $currentPosition, $newPlacement, $newPosition, $legPosition);

        if ($plan != 'Binary') {
            // change leg position of old downlines
            $prefix = config('database.connections.mysql.prefix');
            $updateDownlines = User::where('father_id', $currentPlacement)
                ->where('position', $currentPosition)
                ->selectRaw("({$prefix}users.position - 1) as position, ({$prefix}users.position - 1) as leg_position, id")
                ->orderBy('position')
                ->get();
            foreach ($updateDownlines as $key => $value) {
                User::where('id', $value->id)
                    ->update(['position' => $value->position, 'leg_position' => $value->leg_position]);
            }
        }

        return true;
    }

    public function deleteUpDowns($downlines, $ancestors)
    {
        Treepath::whereIn('descendant', $downlines)
            ->whereIn('ancestor', $ancestors)
            ->delete();

        return true;
    }

    public function changeFatherAndPosition($user, $currentPlacement, $currentPosition, $newPlacement, $newPosition, $legPosition)
    {
        $user->leg_position = $legPosition;
        $user->father_id = $newPlacement->id;
        $user->position = $newPosition;
        $user->save();

        $placementChangeHistory = new PlacementChangeHistory;
        $placementChangeHistory->user_id = $user->id;
        $placementChangeHistory->old_placement_id = $currentPlacement;
        $placementChangeHistory->new_placement_id = $newPlacement->id;
        $placementChangeHistory->old_position = $currentPosition;
        $placementChangeHistory->new_position = $newPosition;
        $placementChangeHistory->save();

        return true;
    }

    public function changeSponsor($user, $newSponsor, $currentSponsor)
    {
        $downlines = SponsorTreepath::where('ancestor', $user->id)
            ->select('descendant')
            ->get()->map(fn ($downline) => $downline->descendant)->toArray();
        $ancestors = SponsorTreepath::where('descendant', $user->id)
            ->where('ancestor', '!=', $user->id)
            ->select('ancestor')->get()->map(fn ($ancestor) => $ancestor->ancestor)->toArray();
        // dd($ancestors);
        $this->deleteSponsorUpDowns($downlines, $ancestors);

        $newSponsorAncestors = SponsorTreepath::where('descendant', $newSponsor->id)
            ->select('ancestor')
            ->get()->map(fn ($newUp) => $newUp->ancestor);

        $newTreePathData = [];
        foreach ($newSponsorAncestors as $ancestor) {
            foreach ($downlines as $descendant) {
                $created_at = $updated_at = now();
                $newTreePathData[] = compact('ancestor', 'descendant', 'created_at', 'updated_at');
            }
        }
        SponsorTreepath::insert($newTreePathData);

        $this->changeSponsorId($user, $newSponsor);
        $oldUserSponsorDepth = $currentSponsor->sponsor_level;
        // dump($oldUserSponsorDepth);
        $newSponsorDepth = $user;
        $addLevel = $newSponsorDepth->sponsor_level - $oldUserSponsorDepth;

        if ($addLevel != 0) {
            User::whereIn('id', $downlines)
                ->increment('sponsor_level', $addLevel);
        }

        return true;
    }

    protected function deleteSponsorUpDowns($downlines, $ancestors)
    {
        SponsorTreepath::whereIn('descendant', $downlines)
            ->whereIn('ancestor', $ancestors)
            ->delete();

        return true;
    }

    public function changeSponsorId($user, $newSponsor)
    {
        $currentSponsorId = $user->sponsor_id;

        $user->sponsor_id = $newSponsor->id;
        $user->save();

        $sponsorChnageHistory = new SponsorChangeHistory;
        $sponsorChnageHistory->user_id = $user->id;
        $sponsorChnageHistory->old_sponsor_id = $currentSponsorId;
        $sponsorChnageHistory->new_sponsor_id = $newSponsor->id;
        $sponsorChnageHistory->save();
    }
    public function checkPlacementAncestor(int $placement, int $sponsorId)
    {
        $checkPositionAncestor = User::with('ancestors')->find($placement);
        if (!$checkPositionAncestor || $checkPositionAncestor->ancestors->where('id', $sponsorId)->isEmpty()) {
            return false;
        }
        return true;
    }
    public function gnealogyTreeDownline(User $user, $moduleStatus, $width, $type = 'tree')
    {
        $coreController = new CoreInfController;
        $treeDepth  = $coreController->configuration()->tree_depth;
        $prefix     = config('database.connections.mysql.prefix');
        $treeQry    = User::query();
        $da         = $treeQry->with(['package','userDetails'])
                        ->when($moduleStatus['rank_status'], fn($query) => $query->with('rankDetail'))
                        ->when($moduleStatus['mlm_plan'] == 'Binary', fn($query) => $query->with('legDetails'))
                        ->when($moduleStatus['mlm_plan'] == 'Stair_Step', fn($query) => $query->with('pvDetails'), fn($qry) => $qry->select("users.personal_pv", "users.group_pv as gpv", "users.product_id", "users.oc_product_id"))
                        ->when($moduleStatus['mlm_plan'] == 'Donation', fn($query) => $query->addSelect('donation_rates.name as donationLevel')
                            ->leftJoin('donation_levels', 'users.id', '=', 'donation_levels.user')
                            ->leftJoin('donation_rates', 'donation_rates.id', '=', 'donation_levels.level')->groupBy('donationLevel'))
                        ->when($type == 'tree', fn($query) => $query->selectRaw("{$prefix}users.id, {$prefix}users.username,{$prefix}users.position,{$prefix}users.user_rank_id, {$prefix}users.active, {$prefix}users.date_of_joining, {$prefix}users.father_id,GROUP_CONCAT(DISTINCT CONCAT({$prefix}uplines.user_level - {$user->user_level}, LPAD({$prefix}uplines.leg_position, 8 , 0)) ORDER BY {$prefix}uplines.user_level SEPARATOR '-') as tree_rank, COALESCE(MAX({$prefix}uplines2.leg_position), 0) as leg_position_rank")
                            ->addselect("treepath1.depth as depth")
                            ->with('fatherDetails')
                            ->join('treepaths as treepath1', 'treepath1.descendant', '=', 'users.id')
                            ->join('treepaths as treepath2', 'treepath2.descendant', '=', 'treepath1.descendant')
                            ->whereRaw("({$prefix}users.user_level - {$user->user_level}) < {$treeDepth}")
                            ->where('treepath1.ancestor', $user->id)
                            ->leftjoin('users AS uplines2', fn($join) => $join->on('uplines2.id', '=', 'treepath2.ancestor')->where('uplines2.id', '!=', $user->id))
                            ->join('users AS uplines', fn($join) => $join->on('uplines.id', '=', 'treepath2.ancestor')->where('uplines.user_level', '>=', $user->user_level))
                        )
                        ->when($type == 'sponsor_tree', fn($query) => $query->selectRaw("{$prefix}users.id, {$prefix}users.username, {$prefix}users.position, {$prefix}users.active, {$prefix}users.date_of_joining, {$prefix}users.sponsor_id,GROUP_CONCAT(DISTINCT CONCAT({$prefix}uplines.sponsor_level - {$user->sponsor_level}, LPAD({$prefix}uplines.leg_position, 8, 0), {$prefix}treepath2.ancestor ) ORDER BY {$prefix}uplines.sponsor_level SEPARATOR '-') as tree_rank")
                            ->selectRaw("({$prefix}users.sponsor_level - {$user->sponsor_level}) depth")
                            ->join('sponsor_treepaths as treepath1', 'treepath1.descendant', 'users.id')
                            ->join('sponsor_treepaths as treepath2', 'treepath2.descendant', '=', 'treepath1.descendant')
                            ->whereRaw("{$prefix}users.sponsor_level - {$user->sponsor_level} < 4")
                            ->where('treepath1.ancestor', $user->id)
                            ->join('users AS uplines', fn($join) => $join->on('uplines.id', '=', 'treepath2.ancestor')
                            ->where('uplines.sponsor_level', '>=', $user->sponsor_level))
                        )
                        ->withCount(['children', 'package'])
                        ->groupBy('users.id')
                        ->where('treepath1.depth', '<', $treeDepth)
                        ->having('leg_position_rank', '<=', $width)
                        ->orHaving('leg_position_rank', null)
                        ->orderBy('tree_rank')
                        ->get();
        return $da;

    }

    public function getUsersDepth($fatherData, $user)
    {
        try {
            $treepathInsertData = $fatherData->ancestors;
        if ($treepathInsertData->isEmpty()) {
            $treePathData = collect([
                $user->id => ['depth' => 0],
                $fatherData->id => ['depth' => 0],
            ]);
        } else {
            $treePathData = $treepathInsertData->mapWithKeys(fn ($da, $key) => [$da->id => ['depth' => $user->user_level - $da->user_level], $user->id => ['depth' => 0]]);
        }
        dd($treePathData);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function renderSponsorTreeTempNode($node, $type)
    {
        $coreInfController = new CoreInfController;
        $treeDepth         = $coreInfController->configuration()->tree_depth;
        $treeWidth         = $coreInfController->configuration()->tree_width;
        $moduleStatus       = $coreInfController->moduleStatus();

        if (($node['level'] < $treeDepth) && $node['childrenCount'] >= $treeWidth) {
            if ($node['level'] == $this->currentLevel) {
                $this->graph .= '</li>';
            }
            if ($node['level'] > $this->currentLevel) {
                $this->graph .= '<ul>';
            }
            if ($node['level'] < $this->currentLevel) {
                $this->graph .= str_repeat('</li></ul>', $this->currentLevel - $node['level']);
            }

            $more = $node['childrenCount'] - $treeWidth;
            if ($more) {
                $position = $node['position'] - 1;
                $this->graph .= "<li class='more-btn' id='more-children-li-{$node['father']}'><a href='javascript:void(0)' id='more-{$node['father']}'><img class='more-image' width='60' src='/assets/images/users/avatar-1.jpg' alt='Card image'><span class='label more-btn' id='more-child-{$node['father']}' data-username='{$node['username']}' data-clicked='false' onClick='getSponsorSiblings({$node['father']}, {$position})'><strong id='more-count-{$node['father']}'>{$more}</strong>  More ></span></a></li>";
            }
            $this->currentLevel = $node['level'];
        }
    }
}
