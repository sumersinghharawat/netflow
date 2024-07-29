<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Menu;
use Illuminate\Support\Arr;
use App\Models\EmployeeMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class MenuPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! request()->ajax()) {
            $prefix = config('database.connections.mysql.prefix');
            $userType   = auth()->user()->user_type;
            if (Cache::has("{$prefix}menuitems")) {
                $menuitems = Cache::get("{$prefix}menuitems");
            } else {
                $menuitems = Menu::with('children.permission', 'permission')->has('permission', '=', 1)->where('react_only', 0)->where('side_menu', 1)->orderBy('order')->get();
                Cache::forever("{$prefix}menuitems", $menuitems);
            }
            $settingsRoutes = config('mlm.settings_route');
            $currentRouteName = Route::current()->getName();

            if ($menuitems->where('route_name', $currentRouteName)->first()) {
                if (auth()->user()->user_type == 'admin') {
                    $routePermission = $menuitems->where('route_name', $currentRouteName)->first()->permission->admin_permission;
                } else if(auth()->user()->user_type == 'employee') {
                    $menuPermission = $menuitems->where('route_name', $currentRouteName)->first()->load('employeePermission');
                    $routePermission = $menuPermission->employeePermission ?? false;
                }
            } elseif (Arr::has($settingsRoutes, $currentRouteName)) {
                $routePermission = Menu::with('children', 'permission')->where('route_name', $currentRouteName)->first()->permission->admin_permission;
            } elseif ($currentRouteName == 'insert.dummy') {
                $routePermission = true;
            } else {
                $routePermission = true;
            }
            if ($routePermission) {
                return $next($request);
            }
            abort(401);
        }

        return $next($request);
    }
}
