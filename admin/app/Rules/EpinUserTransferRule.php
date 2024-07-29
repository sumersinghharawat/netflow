<?php

namespace App\Rules;

use App\Models\PinNumber;
use Illuminate\Contracts\Validation\Rule;

class EpinUserTransferRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $value = PinNumber::where('allocated_user', $value)->where('purchase_status', true)->count();

        return $value != 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'user have no active epins';
    }
}
