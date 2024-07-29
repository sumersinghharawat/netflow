<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\UserActivityJob;
use App\Models\ModuleStatus;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AuthenticatedSessionController extends Controller
{
    public function create($username = '')
    {
        return view('auth.login', compact('username'));
    }

    public function lockscreen($username = "")
    {
        $user = '';
        $user_session = session()->get('user') ?? '';
        if($user_session)
            $user = $user_session[0];
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_compensation")) {
            Cache::forget("{$prefix}_compensation");
        }
        if (Cache::has("{$prefix}_moduleStatus")) {
            Cache::forget("{$prefix}_moduleStatus");
        }
        if (Cache::has("{$prefix}_configurations")) {
            Cache::forget("{$prefix}_configurations");
        }
        if (Cache::has("{$prefix}_menuitems")) {
            Cache::forget("{$prefix}_menuitems");
        }
        if (Cache::has("{$prefix}_employeeMenus")) {
            Cache::forget("{$prefix}_employeeMenus");
        }

        return view('auth.lock-screen', compact('user'));
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        session()->forget('auth_attempt_status');

        session()->forget('auth_attempts');

        $moduleStatus  = ModuleStatus::first();

        $prefix        = str_replace('_','',config('database.connections.mysql.prefix'));


        $request->session()->put('prefix', $prefix);

        Cache::forever("{$prefix}_moduleStatus", $moduleStatus);

        UserActivityJob::dispatch(auth()->user()->id, [], 'loggedIn', auth()->user()->username . ' logged in', config('database.connections.mysql.prefix'), auth()->user()->user_type);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request)
    {
        $prefix = config('database.connections.mysql.prefix');
        UserActivityJob::dispatch(
            auth()->user()->id,
            [],
            'Sign out',
            auth()->user()->username . ' sign out',
            "{$prefix}",
            $prefix,
            auth()->user()->user_type,
        );
        $moduleStatus = ModuleStatus::first();
        if(auth()->user()->user_type == 'admin' && $moduleStatus->ecom_status) {
            DB::table('oc_session')->where('customer_id', auth()->user()->ecom_customer_ref_id)->delete();
        }
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        Cache::flush();
        if (Cache::has("{$prefix}compensation")) {
            Cache::forget("{$prefix}compensation");
        }
        if (Cache::has("{$prefix}moduleStatus")) {
            Cache::forget("{$prefix}moduleStatus");
        }
        if (Cache::has("{$prefix}configurations")) {
            Cache::forget("{$prefix}configurations");
        }
        if (Cache::has("{$prefix}menuitems")) {
            Cache::forget("{$prefix}menuitems");
        }
        if (Cache::has("{$prefix}employeeMenus")) {
            Cache::forget("{$prefix}employeeMenus");
        }
        if (Cache::has("{$prefix}countries")) {
            Cache::forget("{$prefix}countries");
        }

        return redirect('/');

    }

    public function destroy1(Request $request)
    {
        $prefix = session()->get('prefix');
        $user = auth()->user()->load('userDetail');
        DB::purge('mysql');
        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::connection('mysql');
        UserActivityJob::dispatch(
            auth()->user()->id,
            [],
            'Sign out',
            auth()->user()->username . ' sign out',
            "{$prefix}_",
            $prefix,
            auth()->user()->user_type,
        );

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        if (Cache::has("{$prefix}_compensation")) {
            Cache::forget("{$prefix}_compensation");
        }
        if (Cache::has("{$prefix}_moduleStatus")) {
            Cache::forget("{$prefix}_moduleStatus");
        }
        if (Cache::has("{$prefix}_configurations")) {
            Cache::forget("{$prefix}_configurations");
        }
        if (Cache::has("{$prefix}_menuitems")) {
            Cache::forget("{$prefix}_menuitems");
        }
        if (Cache::has("{$prefix}_employeeMenus")) {
            Cache::forget("{$prefix}_employeeMenus");
        }
        Cache::flush();
        session()->push('user',[
            'username'  => $user->username,
            'fullname'  => $user->userDetail->name . ' ' . $user->userDetail->second_name ?? '' ,
            'image'     => $user->image

        ]);
        return redirect('/');
    }
}
