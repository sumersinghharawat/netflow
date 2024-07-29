<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use App\Models\Package;
use App\Models\OCProduct;
use App\Models\UploadImage;
use App\Traits\ImageUpload;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Models\ToolTipConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RequestsTooltipUpdate;
use App\Models\MonolineConfig;

class TreeSettingsController extends CoreInfController
{
    use ImageUpload;
    use UploadTraits;
    public function index()
    {
        $module_status = $this->moduleStatus();
        $rank_details = null;
        $reentry_icon = null;
        $tree_icon_based_on = $this->configuration()->tree_icon_based;
        if ($module_status->mlm_plan == 'Monoline') {
            $reentry_icon = MonolineConfig::first()->tree_icon ?? "/assets/images/users/avatar-1.jpg";
        }
        if ($tree_icon_based_on == 'profile_image') {
            $tree_icon_view = view('admin.settings.advancedSettings.tooltip.profile-image');
        }
        if ($tree_icon_based_on == "member_pack") {
            if ($module_status->ecom_status) {
                $tree_icons         =   OCProduct::select('product_id', 'model', 'tree_icon')->where('status', 1)->where('package_type', 'registration')->get();
            } else {
                $tree_icons = Package::select('id', 'name', 'tree_icon')->get();
            }
        }
        if ($tree_icon_based_on == 'rank') {
            if ($module_status->rank_status) {
                $tree_icons                    =    Rank::select('id', 'name', 'tree_icon')->get();
            }
        }
        $tool_tip_items = ToolTipConfig::select('id', 'name', 'status', 'slug', 'view_status')->where('view_status',1)->get();

        $member_status = Configuration::all()->first();
        $image1 = UploadImage::where('imageable_id', $member_status->id)
            ->where('imageable_type', 'App\Models\Configuration')
            ->where('image_type', 'active_tree_icon')
            ->first();
        $image2 = UploadImage::where('imageable_id', $member_status->id)
            ->where('imageable_type', 'App\Models\Configuration')
            ->where('image_type', 'inactive_tree_icon')
            ->first();
        return view('admin.settings.advancedSettings.tooltip.index', compact('tool_tip_items', 'module_status', 'member_status', 'rank_details', 'image1', 'image2', 'tree_icon_based_on', 'reentry_icon'));
    }

    public function update_config(Request $request)
    {
        $configuration                      =   $this->configuration();
        $configuration->update(['tree_icon_based' => $request->config]);

        $module_status      = $this->moduleStatus();
        $treeIconBasedOn    = $this->configuration()->tree_icon_based;
        if ($module_status->ecom_status) {
            $membership_packages         = OCProduct::select('product_id as id', 'model as name', 'tree_icon')->where('status', 1)->where('package_type', 'registration')->get();
        } else {
            $membership_packages         = Package::select('id', 'name', 'tree_icon')->ActiveRegPackage()->get();
        }
        $member_status                   = Configuration::all()->first();
        $activeImg                       = UploadImage::where('imageable_id', $member_status->id)
            ->where('imageable_type', 'App\Models\Configuration')
            ->where('image_type', 'active_tree_icon')
            ->first();
        $inactiveImg                     = UploadImage::where('imageable_id', $member_status->id)
            ->where('imageable_type', 'App\Models\Configuration')
            ->where('image_type', 'inactive_tree_icon')
            ->first();
        if ($treeIconBasedOn == 'member_status') {
            $tree_icon_view = view('admin.settings.advancedSettings.tooltip.member-status', compact('member_status', 'activeImg', 'inactiveImg'));
            $image          = [
                'active' => ($activeImg) ? Storage::url($activeImg->image) : url('assets/images/users/active-user.jpg'),
                'inactive' => ($inactiveImg) ? Storage::url($inactiveImg->image) : url('assets/images/users/inactive-user.jpg'),
            ];
        }
        if ($treeIconBasedOn == 'profile_image') {
            $tree_icon_view = view('admin.settings.advancedSettings.tooltip.profile-image');
            $image = [];
        }
        if ($treeIconBasedOn == 'member_pack') {
            $tree_icon_view = view('admin.settings.advancedSettings.tooltip.member-package', compact('membership_packages'));
            $newpck = $membership_packages->map(fn($item) => !$item->tree_icon ? ['id' => $item->id, 'name' => $item->name, 'tree_icon' => url('assets/images/users/active-user.jpg')] : $item);

            $image = $newpck;
        }
        if ($treeIconBasedOn == "rank") {
            if ($module_status->rank_status) {
                $rank_details                   =    Rank::select('id', 'name', 'tree_icon')->get();
                $tree_icon_view  =  view('admin.settings.advancedSettings.tooltip.rank-details', compact('rank_details'));
                $image = $rank_details;
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'criteria'  => $treeIconBasedOn,
                'view'      => $tree_icon_view->render(),
                'image'     => $image
            ]
        ]);
    }

    public function update(RequestsTooltipUpdate $request)
    {
        $tooltip = ToolTipConfig::Active()->update(['status' => 0]);
        ToolTipConfig::whereIn('id', $request->all()['tooltip'])
            ->update(['status' => 1]);

        return back()->with('success', 'Updated successfully.');
    }

    public function MembershipPackImage(Request $request, $id)
    {
        $moduleStatus         =       $this->moduleStatus();
        if ($moduleStatus->ecom_status) {
            $membershipPackage         = OCProduct::where('status', 1)->where('package_type', 'registration')->where('product_id', $id)->firstOrfail();
        } else {
            $membershipPackage         = Package::findOrfail($id);
        }
        if ($moduleStatus->ecom_status) {
            if ($request->hasFile('membership_pack_image')) {
                $file       = $request->file('membership_pack_image');
                $model      = $membershipPackage;
                $prefix     = 'treeIcon';
                $folder     = 'treeIcon';
                if (!$this->treeIconUpload($file, $model, $prefix, $folder)) {
                    if ($request->ajax()) {
                        return response()->json([
                            'status'    => false,
                            'message'   => 'Tree Icon file upload failed.'
                        ], 400);
                    }
                }
            }
        } else {
            if ($request->hasFile('membership_pack_image')) {
                $file       = $request->file('membership_pack_image');
                $model      = $membershipPackage;
                $prefix     = 'treeIcon';
                $folder     = 'treeIcon';
                if (!$this->treeIconUpload($file, $model, $prefix, $folder)) {
                    if ($request->ajax()) {
                        return response()->json([
                            'status'    => false,
                            'message'   => 'Tree Icon file upload failed.'
                        ], 400);
                    }
                }
            }
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Tree Icon file uploaded.'
        ]);
        return back()
            ->with('success', 'Image uploaded successfully.');
    }

    public function update_rank(Request $request, $id)
    {
        if ($request->hasFile('rank_pic')) {
            $rank       =  Rank::find($id);
            $file       = $request->file('rank_pic');
            $model      = $rank;
            $prefix     = 'treeIcon';
            $folder     = 'treeIcon';
            if (!$this->treeIconUpload($file, $model, $prefix, $folder)) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Tree Icon file upload failed.'
                    ], 400);
                }
            }
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Tree Icon file uploaded.'
        ]);
    }

    public function updateTreeSize(Request $request)
    {
        $validated = $request->validate([
            'depth'     => 'required|numeric|min:3|max:7',
            'width'     => 'required|numeric|min:3|max:10'
        ]);
        $prefix = session()->get('prefix');
        $configuration = $this->configuration();
        $configuration->tree_width = $validated['width'];
        $configuration->tree_depth = $validated['depth'];
        $configuration->save();
        Cache::forever("{$prefix}_configurations", $configuration);
        return back()->with('success', 'Successfully Updated.');
    }
}
