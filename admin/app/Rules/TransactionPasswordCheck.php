<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class TransactionPasswordCheck implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $transferFrom;

    public function __construct($inputs)
    {
        $this->transferFrom = $inputs;
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
        if (! array_key_exists('transfer_from', $this->transferFrom)) {
            return false;
        }
        $userPassword = User::with('transPassword')->find($this->transferFrom['transfer_from']);
        if ($userPassword && Hash::check($value, $userPassword->transPassword->password)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Transaction Password not correct, Please try again.';
    }
}
