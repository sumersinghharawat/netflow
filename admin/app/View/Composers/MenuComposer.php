<?php

namespace App\View\Composers;

use App\Models\EmployeeMenu;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuComposer
{
    /**
     * @param  View  $view
     */
    public function compose(View $view)
    {
        $prefix = config('database.connections.mysql.prefix');

        if (Auth::user()->user_type == 'admin') {
            if (Cache::has("{$prefix}menuitems")) {
                $menuitems = Cache::get("{$prefix}menuitems");
            } else {
                $menuitems = Menu::with('children.permission', 'permission')->has('permission', '=', 1)->where('react_only', 0)->where('side_menu', 1)->orderBy('order')->get();
                Cache::forever("{$prefix}menuitems", $menuitems);
            }
            if (Auth::guard('web')) {
                $view->with('menuitems', $menuitems);
            }
        } elseif (Auth::user()->user_type == 'employee') {
            $username = auth()->user()->username;
            if (Cache::has("{$prefix}{$username}_employeeMenus")) {
                $employeeMenus = Cache::get("{$prefix}{$username}_employeeMenus");
            } else {
                $employeeMenus = EmployeeMenu::with('menuDetails')->where('employee_id', Auth::user()->id)->get();
                Cache::forever("{$prefix}{$username}_employeeMenus", $employeeMenus);
            }
            $view->with('employeeMenus', $employeeMenus);
        }
    }
}
