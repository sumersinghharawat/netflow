<?php

namespace App\View\Composers;

use App\Models\CurrencyDetail;
use App\Models\ModuleStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CurrencyComposer
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
        if ($moduleStatus->multi_currency_status) {
            $currencies = CurrencyDetail::Active()->get();
            $view->with(compact('currencies'));
        }
    }
}
