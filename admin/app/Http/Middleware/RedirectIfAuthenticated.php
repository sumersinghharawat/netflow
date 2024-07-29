<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? ['web'] : $guards;
        foreach ($guards as $guard) {
            switch ($guard) {
                case 'web':
                    if (Auth::guard($guard)->check()) {
                        if (auth()->user()->user_type == 'admin') {
                            return redirect(RouteServiceProvider::HOME);
                        } elseif (auth()->user()->user_type == 'employee') {
                            return redirect()->route('employee.dashboard');
                        }
                    }
                    break;

                default:
                    break;
            }
        }

        return $next($request);
    }
}
