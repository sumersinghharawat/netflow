<?php

namespace App\View\Composers;

use App\Models\CompanyProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanyProfileComposer
{
    /**
     * @param  View  $view
     */
    public function compose(View $view)
    {
        $companyProfile = CompanyProfile::first();

        $view->with(compact('companyProfile'));
    }
}
