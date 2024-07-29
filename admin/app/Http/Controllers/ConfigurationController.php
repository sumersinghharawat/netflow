<?php

namespace App\Http\Controllers;

use App\Models\CommissionStatusHistory;
use App\Models\EmployeeMenu;
use App\Models\Language;
use App\Models\Menu;
use App\Models\ModuleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Throwable;
use Illuminate\Support\Facades\DB;

class ConfigurationController extends CoreInfController
{
    public function updateLanguage(Request $request)
    {
        try {
            Language::Active()->first()->update(['status' => 0]);

            Language::where('code', $request->language)->update(['status' => 1]);
            App::setLocale($request->language);
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Language Updated Successfully',
                    'data' => $request->language,
                ], 200);
            }
        } catch (Throwable $th) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => $th,

                ], 400);
            }
        }
    }

    public function mangeMenu()
    {
        $menus = Menu::where('is_heading', 1)->with('permission', 'children.permission')->orderBy('order', 'ASC')->get();

        return view('configuration.menu-permission', compact('menus'));
    }

    public function menuUpdate(Request $request)
    {
        $menu                   = Menu::with('permission')->find($request->menu);
        $menu->order            = $request->order;
        $menu->admin_icon       = $request->admin_icon;
        $menu->user_icon        = $request->user_icon;
        $menu->admin_only       = ($request->has('admin_only')) ? 1 : 0;
        $menu->react_only       = ($request->has('react_only')) ? 1 : 0;
        $menu->child_order      = ($request->has('child_order')) ? $request->child_order : $menu->child_order;

        $menu->save();
        $permission         = $menu->permission;
        $permission->admin_permission = ($request->has('admin_permission')) ? 1 : 0;
        $permission->user_permission  = ($request->has('user_permission')) ? 1 : 0;
        $permission->save();
        return redirect()->back()->with('success', 'Menu successfully updated');
    }

    public function manageCommission(Request $request)
    {
        $status = CommissionStatusHistory::query();

        if ($request->type == 'initialised') {
            $status->Initialised();
        } elseif ($request->type == 'processing') {
            $status->Processing();
        } elseif ($request->type == 'failed') {
            $status->Failed();
        } elseif ($request->type == 'success') {
            $status->Success();
        }
        $commissions = $status->with('user:username,id')->latest()->paginate(10)->withQueryString();
        return view('configuration.manage-commission', compact('commissions'));
    }

    public function manageModuleStatus()
    {
        $moduleStatus = $this->moduleStatus();


        return view('configuration.manage-module-status', compact('moduleStatus'));
    }

    public function updateModules(Request $request)
    {
        try {
            DB::beginTransaction();
            $field = $request->field;
            $moduleStatus = ModuleStatus::first();
            $moduleStatus->$field = $request->status;
            $moduleStatus->push();
            // $menu = $this->updateMenu($field, $request->status);
            // if ($menu) {
            DB::commit();
            return true;
            // }
            // return false;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
        }
    }


    public function updateMenu($field, $status)
    {
        try {
            switch ($field) {
                case 'pin_status':
                    $slug = 'e-pin';
                    break;
                case 'product_status':
                    $slug = 'package';
                    break;
                case 'employee_status':
                    $slug = 'privileged-user';
                default:
                    $slug = 'NA';
                    break;
            }

            $menu = Menu::with('permission')->where('slug', $slug)->first();
            if (isset($menu)) {
                $menu->permission->admin_permission = $status;
                $menu->permission->user_permission  = $status;
                $menu->push();
                if (!$status)
                    EmployeeMenu::where('menu_id', $menu->id)->delete();
            }


            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}
