<?php

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\DemoUser;
use App\Models\PinConfig;
use App\Models\PinNumber;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\ModuleStatus;
use App\Models\CurrencyDetail;
use App\Models\UsernameConfig;
use App\Services\StripeService;
use Illuminate\Support\Facades\DB;
use App\Models\PendingRegistration;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentGatewayDetail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

if (!function_exists('generateUsername')) {
    function generateUsername($minLength, $maxLength)
    {
        $usernameConfig = UsernameConfig::first();
        if ($usernameConfig->prefix_status && $usernameConfig->prefix != null) {
            $generateNumber = $maxLength - strlen($usernameConfig->prefix);

            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $generateNumber; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            $username = $usernameConfig->prefix . $randomString;
        } elseif (!$usernameConfig->prefix_status) {
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $minLength + 1; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 2)];
            }
            $username = $randomString;
        }

        $checkUserTable = User::where('username', $username)->exists();
        $checkPending = PendingRegistration::where('username', $username)->exists();
        if ($checkUserTable || $checkPending) {
            generateUsername($minLength, $maxLength);
        }
        return $username;
    }
}

if (!function_exists('generateUsernameExternal')) {
    function generateUsernameExternal($minLength, $maxLength)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $minLength + 1; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 2)];
        }
        $username = $randomString;

        $checkUserTable = DemoUser::where('username', $username)->exists();
        // $checkPending = PendingRegistration::where('username', $username)->exists();
        if ($checkUserTable) {
            generateUsernameExternal($minLength, $maxLength);
        }

        return $username;
    }
}

if (!function_exists('generatePinNumber')) {
    function generatePinNumber()
    {
        $pinConfig = PinConfig::first();
        $pinLength = $pinConfig->length;
        $caharacterSet = $pinConfig->character_set;
        $charset = '';
        $randomPinNumber = '';

        if ($caharacterSet == 'alphabet') {
            $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } elseif ($caharacterSet == 'numeral') {
            $charset = '0123456789';
        } else {
            $charset .= 'ABCDEFGHIJKLMNPQRSTUVWXYZ';
            $charset .= '23456789';
        }
        for ($i = 0; $i < $pinLength; $i++) {
            $randomPinNumber .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
        }
        if (PinNumber::where('numbers', $randomPinNumber)->exists()) {
            generatePinNumber();
        }

        return trim($randomPinNumber);
    }
}

if (!function_exists('generateTransactionNumber')) {
    function generateTransactionNumber($length = 16)
    {
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($charset);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $charset[rand(0, $charactersLength - 1)];
        }
        if (Transaction::where('transaction_id', $randomString)->exists()) {
            generateTransactionNumber();
        }

        return $randomString;
    }
}
if (!function_exists('makeInvoice')) {
    function makeInvoice($last_inserted_id)
    {
        $invoice_no = 1000 + $last_inserted_id;
        $invoice_no = 'RPCHSE' . $invoice_no;

        return $invoice_no;
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount = 0)
    {
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_moduleStatus")) {
            $moduleStatus = Cache::get("{$prefix}_moduleStatus");
        } else {
            $moduleStatus = ModuleStatus::first();
            Cache::forever("{$prefix}_moduleStatus", $moduleStatus);
        }
        if ($moduleStatus->multi_currency_status && Cache::has($prefix . '_userCurrency')) {
            $currency = Cache::get($prefix . '_userCurrency');
            $amount = $amount * $currency->value;
        }
        $amount = floatval($amount);

        return round($amount, 2);
    }
}

if (!function_exists('formatCurrencyIntoNew')) {
    function formatCurrencyIntoNew($amount, $currency)
    {
        $prefix = session()->get('prefix');
        $id = auth()->user()->id;

        if (Cache::has("{$prefix}_moduleStatus")) {
            $moduleStatus = Cache::get("{$prefix}_moduleStatus");
        } else {
            $moduleStatus = ModuleStatus::first();
            Cache::forever("{$prefix}_moduleStatus", $moduleStatus);
        }
        if ($moduleStatus->multi_currency_status && Cache::has($id . '_userCurrency') && !Cache::get($id . '_userCurrency')->default) {
            $userCurrency = Cache::get($id . '_userCurrency');
            $baseValue = $amount / $userCurrency->value;
            $newCurrency = CurrencyDetail::where('code', $currency)->orWhere('id', $currency)->first();
            $amount = $baseValue * $newCurrency->value;
        }

        return $amount;
    }
}

if (!function_exists('currencySymbol')) {
    function currencySymbol($authUser = null)
    {
        $prefix = session()->get('prefix');
        if (Cache::has("{$prefix}_moduleStatus")) {
            $moduleStatus = Cache::get("{$prefix}_moduleStatus");
        } else {
            $moduleStatus = ModuleStatus::first();
            Cache::forever("{$prefix}_moduleStatus", $moduleStatus);
        }
        if ($moduleStatus->multi_currency_status && auth()->user()->default_currency) {
            $currency = auth()->user()->currency;
        } else {
            if((Cache::has("{$prefix}_default_currency"))){
                $currency = Cache::get("{$prefix}_default_currency");
            } else {
                $currency = CurrencyDetail::where('default', 1)->first();
                Cache::put("{$prefix}_default_currency", $currency);
            }
        }

        return ($currency) ? $currency->symbol_left : '$';
    }
}

if (!function_exists('defaultCurrency')) {
    function defaultCurrency($amount = 0)
    {
        $prefix = session()->get('prefix');

        if (Cache::has("{$prefix}_moduleStatus")) {
            $moduleStatus = Cache::get("{$prefix}_moduleStatus");
        } else {
            $moduleStatus = ModuleStatus::first();
            Cache::forever("{$prefix}_moduleStatus", $moduleStatus);
        }
        if ($moduleStatus->multi_currency_status && auth()->user()->default_currency) {
            $currency = auth()->user()->load('currency')->currency;
        } else {
            $currency = CurrencyDetail::where('default', 1)->first();
        }
        if ($currency) {
            return  round($amount / $currency->value, 8);
        } else {
            return null;
        }
    }
}

if (!function_exists('stripePublicKey')) {
    function stripePublicKey()
    {
        $stripeService = new StripeService;
        $stripePublicKey = $stripeService->getStripeCredentials()->public_key;
        return $stripePublicKey;
    }
}

if (!function_exists('formatNumberShort')) {
    function formatNumberShort($amount, $precision = 2): string
    {
        $amount = floatval($amount);
        if ($amount < 900) {
            // 0 - 900
            $amount_format = number_format($amount, $precision);
            $suffix = '';
        } elseif ($amount < 900000) {
            // 0.9k-850k
            $amount_format = number_format($amount / 1000, $precision);
            $suffix = 'K';
        } elseif ($amount < 900000000) {
            // 0.9m-850m
            $amount_format = number_format($amount / 1000000, $precision);
            $suffix = 'M';
        } elseif ($amount < 900000000000) {
            // 0.9b-850b
            $amount_format = number_format($amount / 1000000000, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $amount_format = number_format($amount / 1000000000000, $precision);
            $suffix = 'T';
        }

        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $amount_format = str_replace($dotzero, '', $amount_format);
        }

        return $amount_format . $suffix;
    }
}
if (!function_exists('encryptData')) {
    function encryptData($data): string
    {
        $pathToPublicKey = Storage::disk('local')->path('public.pem');
        $publicKey = file_get_contents($pathToPublicKey);
        $encodedData = json_encode($data);
        openssl_public_encrypt(
            $encodedData,
            $encryptedData,
            $publicKey
        );

        return base64_encode($encryptedData);
    }
}
if (!function_exists('getStoreString')) {
    function getStoreString($isUser = false)
    {
        $string = Str::random(30);
        $checkExists = DB::table('string_validators')->where('string', $string)->count();
        if ($checkExists) {
            getStoreString();
        }
        if (auth()->user()->user_type == 'employee') {
            $userid = User::GetAdmin()->id;
        }else if($isUser){
            $userid = DB::table('users')->where('user_type', 'user')->where('email', auth()->user()->email)->first()->id;
        } else {
            $userid = auth()->user()->id;
        }
        $checkTable = DB::table('string_validators')->where('user_id', $userid)->where('status', 1)->count();
        if ($checkTable) {
            DB::table('string_validators')->where('user_id', $userid)->where('status', 1)->update(['string' => $string]);
        } else {
            DB::table('string_validators')->insert(['user_id' => $userid, 'string' => $string, 'status' => 1]);
        }

        return $string;
    }
}
if (!function_exists('getPayoutInvoiceNo')) {
    function getPayoutInvoiceNo($id)
    {
        return "PR000{$id}";
    }
}
if (!function_exists('isFileExists')) {
    function isFileExists($url)
    {
        // if (isset($url)) {
        //     $http = new Client([
        //         'verify' => false,
        //         'http_errors'     => false,
        //     ]);
        //     $response = $http->get($url);
        //     return ($response->getStatusCode()) == 200 ? true : false;
        // }
        return true;
    }
}
if (!function_exists('getPaypalConfigs')) {
    function getPaypalConfigs()
    {
        $demoStatus = config('mlm.demo_status');
        if ($demoStatus == 'yes') {
            $prefix = config('database.connections.mysql.prefix');
        }

        $paypalDetail = PaymentGatewayDetail::whereHas('gateway', fn ($gateway) => $gateway->where('slug', 'paypal'))->first();

        return [
            'payment_gateway_id'    => $paypalDetail->payment_gateway_id ?? null,
            'client_id'             => $paypalDetail->public_key ?? null,
            'client_secret'         => $paypalDetail->secret_key ?? null,
            'mode' => ($paypalDetail) ? PaymentGatewayConfig::find($paypalDetail->payment_gateway_id)->mode : '',
        ];
    }
}
if (!function_exists('generateDemoPrefix')) {
    function generateDemoPrefix($count = 4)
    {
        $demoUser = DemoUser::latest()->first();
        $prefix     = $demoUser->prefix + $count;
        if (DemoUser::where('prefix', $prefix)->exists()) {
            generateDemoPrefix($count + 1);
        }
        return $prefix;
    }
}
if(!function_exists('generateEmail')){
    function generateEmail(){
        $email = fake()->email();
        if(DemoUser::where('email', $email)->exists()){
            generateEmail();
        }
        return $email;
    }
}
