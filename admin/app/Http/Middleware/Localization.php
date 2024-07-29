<?php

namespace App\Http\Middleware;

use App\Models\ModuleStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class Localization
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
        if (config('mlm.demo_status') == 'yes') {
            $prefix = session()->get('prefix');
            if (Cache::has("{$prefix}_moduleStatus")) {
                $moduleStatus = Cache::get("{$prefix}_moduleStatus");
            } else {
                $moduleStatus = ModuleStatus::first();
                Cache::forever("{$prefix}_moduleStatus", $moduleStatus);
            }
            $locale = 'en';
            if ($moduleStatus->multilang_status) {
                $userLang = auth()->user()->load('locale');
                $locale = ($userLang->locale()->exists())
                                ? $userLang->locale->code
                                : 'en';
            }
        } else {
            $userLang = auth()->user()->load('locale');
            $locale = ($userLang->locale()->exists())
                            ? $userLang->locale->code
                            : 'en';
        }
        App::setLocale($locale);

        return $next($request);
    }
}
