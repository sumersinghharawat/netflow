<?php

namespace App\Rules;

use App\Http\Controllers\CoreInfController as inf;
use App\Models\OCProduct;
use App\Models\Package;
use Illuminate\Contracts\Validation\Rule;

class RegisterPack implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $coreInf = new inf;

        $moduleStatus = $coreInf->moduleStatus();

        if ($moduleStatus->ecom_status) {
            return OCProduct::where('status', 1)->where('package_type', 'registration')->where('product_id', $value)->exists();
        }

        return Package::ActiveRegPackage()->where('id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The selected package must be a registration pack.';
    }
}
