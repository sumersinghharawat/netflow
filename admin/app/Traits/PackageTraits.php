<?php

namespace App\Traits;

use App\Http\Controllers\CoreInfController as inf;
use App\Models\OCProduct;
use App\Models\Package;

trait PackageTraits
{
    public function regPackages()
    {
        $coreInf = new inf;

        $moduleStatus = $coreInf->moduleStatus();

        if ($moduleStatus->ecom_status) {
            return OCProduct::where('status', 1)->where('package_type', 'registration')->get();
        }

        return Package::ActiveRegPackage()->get();
    }
}
