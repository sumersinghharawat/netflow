<?php

namespace App\Http\Requests;

use App\Http\Controllers\CoreInfController;
use App\Models\DemoUser;
use App\Models\PaymentGatewayConfig;
use App\Models\SignupField;
use App\Models\User;
use App\Models\UsernameConfig;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReplicaUserRegisterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(Request $request)
    {
        $prefix = '';
        $demoUser = DemoUser::where('username', $request->user)->first();

        if (! $demoUser) {
            abort(401);
        }
        $prefix = $demoUser->prefix;
        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::purge('mysql');
        DB::connection('mysql');
        $request->session()->put('prefix', $prefix);

        $coreController = new CoreInfController;
        $moduleStatus = $coreController->moduleStatus();

        $customFields = SignupField::orderBy('sort_order', 'ASC')->Active()->get();
        $signupSettings = $coreController->signupSettings($prefix);
        $rules = collect([]);

        $usernameConfig = UsernameConfig::first();
        $length = explode(';', $usernameConfig->length);
        $minLength = $length[0];
        $maxLength = $length[1];
        $rules = collect([
            // 'sponsor_id'                => 'required|exists:users,id',
            // 'sponsorName'               => 'required',
            'product_id' => 'required',
            'first_name' => 'required',
            'username' => 'sometimes|required|unique:users|min:'.$minLength.'|max:'.$maxLength,
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'terms' => 'required',
            'payment_method' => 'required',
            'email' => 'required|email',
            'phone' => 'numeric',
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
