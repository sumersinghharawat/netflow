<?php

namespace App\View\Composers;

use App\Models\ModuleStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ModuleStatusComposer
{
    /**
     * @param  View  $view
     */
    public function compose(View $view)
    {
        $prefix = config('database.connections.mysql.prefix');

        if (Cache::has("{$prefix}moduleStatus")) {
            $moduleStatus = Cache::get("{$prefix}moduleStatus");
        } else {
            $moduleStatus = ModuleStatus::first();
            Cache::forever("{$prefix}moduleStatus", $moduleStatus);
        }

        if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'employee') {
            $view->with('moduleStatus', $moduleStatus);
        }
    }
}
