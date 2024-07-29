<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyToken
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
        $data = 'not authorized';
        $demoStatus = config('mlm.demo_status');
        if ($request->hasHeader('token')) {
            if ($request->header('token') == '1055') {
                if ($demoStatus == 'yes') {
                    if ($request->hasHeader('prefix')) {
                        $prefix = $request->header('prefix');
                        DB::purge('mysql');
                        config(['database.connections.mysql.prefix' => $prefix]);
                        DB::connection('mysql');
                        return $next($request);
                    } else {
                        $data = 'prefix not set';
                    }
                } else {
                    return $next($request);
                }
            } else {
                $data = 'token verifcation failed';
            }
        }


        return response()->json([
            'status' => false,
            'data' => $data,
        ], 401);
    }
}
