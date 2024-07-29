<?php

namespace App\View\Composers;

use App\Models\ModuleStatus;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NotificationComposer
{
    /**
     * @param  View  $view
    */
    public function compose(View $view)
    {
        if (config('mlm.demo_status' == 'yes')) {
            $prefix = session()->get('prefix');
            DB::purge('mysql');
            config(['database.connections.mysql.prefix' => "{$prefix}_"]);
            DB::connection('mysql');
        } else {
            $prefix = config('database.connections.mysql.prefix');
        }
        if (Cache::has("{$prefix}_moduleStatus")) {
            $moduleStatus = Cache::get("{$prefix}_moduleStatus");
        } else {
            $moduleStatus = ModuleStatus::first();
            Cache::forever("{$prefix}_moduleStatus", $moduleStatus);
        }
        $user = User::GetAdmin();
        $notifications = $user->unreadNotifications()->latest()->limit(5)->get();
        $notificationCount = $user->unreadNotifications()->count();
        $items['notificationCount'] = $notificationCount;
        $items['notifications']     = $notifications;
        $view->with('items', $items);
    }
}
