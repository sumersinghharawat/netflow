<?php

namespace App\Http\Middleware;

use Illuminate\Validation\ValidationException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckPreset
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
        $demoUser = collect(DB::select('select * from demo_users where username = ? LIMIT 1', [auth()->user()->username]))->first();
        $demoStatus = config('mlm.demo_status') == 'yes' ? true : false;
        if ($demoUser && $demoStatus && $demoUser->is_preset) {
            if ($request->ajax()) {
                throw ValidationException::withMessages([
                    'errors' => 'You do not have permission to access this resource',
                ], 422);
            } else {
                return redirect()->back()->withErrors("You do not have permission to access this resource");
            }
            
        }
        return $next($request);
    }
}
