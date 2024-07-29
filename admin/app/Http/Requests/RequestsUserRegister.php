<?php

namespace App\Http\Requests;

use App\Http\Controllers\CoreInfController;
use App\Models\PaymentGatewayConfig;
use App\Models\SignupField;
use App\Models\UsernameConfig;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class RequestsUserRegister extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $coreController = new CoreInfController;
        $moduleStatus = $coreController->moduleStatus();

        $customFields = SignupField::orderBy('sort_order', 'ASC')->Active()->get();
        $signupSettings = $coreController->signupSettings();
        $rules = collect([]);

        $usernameConfig = UsernameConfig::first();
        $length = explode(';', $usernameConfig->length);
        $minLength = $length[0];
        $maxLength = $length[1];
        $rules = collect([
            'sponsor_id' => 'required|exists:users,id',
            'sponsorName' => 'required',
            'username' => 'sometimes|required|unique:users|min:'.$minLength.'|max:'.$maxLength,
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'terms' => 'required',
            'payment_method' => 'required',
            'regFromTree' => 'required',
        ]);
        if ($customFields->contains('name', 'date_of_birth') && $signupSettings->age_limit) {
            $customFields = $customFields->filter(fn ($value, $key) => $value->name != 'date_of_birth');
            $ageLimit = Carbon::now()->subYears($signupSettings->age_limit)->format('Y-m-d');
            $rules->put('date_of_birth', "required|date|before_or_equal:$ageLimit");
        }

        $dynamicRule = $customFields->mapWithKeys(fn ($item, $key) => ($item->required) ? [$item->name => 'required'] : []);
        $rules->mapWithKeys(fn ($item, $key) => $dynamicRule->put($key, $item));
        $paymentMethod = PaymentGatewayConfig::find($this->request->all()['payment_method']);
        if ($paymentMethod->slug == 'bank-transfer') {
            $rules->put('reciept', 'required|file');
        } elseif ($paymentMethod->slug == 'stripe') {
            $rules->put('stripeToken', 'required');
        }
        if ($moduleStatus['product_status'] == 'yes') {
            $rules->put('product_id', 'required|exists:packages,id');
        }
        if ($moduleStatus['mlm_plan'] == 'Binary') {
            $rules->put('position', 'required|in:L,R');
        }
        return $rules->toArray();
    }
}
