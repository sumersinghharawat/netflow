<?php

namespace App\View\Composers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DemoStatusComposer
{
    /**
     * @param  View  $view
     */
    public function compose(View $view)
    {
        if(config('mlm.demo_status') == 'yes') {
            $adminUsername = User::GetAdmin();
            $demoDetails = DB::select("SELECT username,is_preset FROM demo_users WHERE `username` = '{$adminUsername->username}'");
            $demoData = [
                'demo_status' => true,
                'isPreset' => $demoDetails[0]->is_preset
            ];
        } else {
            $demoData = [
                'demo_status' => false,
                'isPreset' => 0
            ];
        }
        $view->with(compact('demoData'));

    }
}
