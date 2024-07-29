<?php

namespace App\View\Composers;

use App\Models\MailBox;
use App\Models\ModuleStatus;
use App\Models\CompanyProfile;
use App\Models\PinRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FaviconComposer
{
    /**
     * @param  View  $view
     */
    public function compose(View $view)
    {

        $prefix = session()->get('prefix');
        $demoStatus = config('mlm.demo_status');

        if (!$prefix && $demoStatus == 'yes') {
            $view->with('favicon', '');
        } else {
            if (Cache::has("{$prefix}_companyProfile")) {
                $profile = Cache::get("{$prefix}_companyProfile");
            } else {
                $profile = CompanyProfile::first();
                Cache::forever("{$prefix}_companyProfile", $profile);
            }
            if (isset($profile->favicon)) {
                $favicon = $profile->favicon;
            } else {
                $favicon = '';
            }
            $view->with('favicon', $favicon);
        }
    }
}
