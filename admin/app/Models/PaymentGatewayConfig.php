<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PaymentGatewayConfig extends Model
{
    use HasFactory;

    protected $guarded;

    protected function parseDateWithTimezone($value): Carbon
    {
        return Carbon::parse($value)->timezone(config('mlm.local_timezone'));
    }
    
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    public function ScopeSortAscOrder($query)
    {
        return $query->orderBy('sort_order', 'ASC');
    }

    public function ScopePaymentonly($query)
    {
        return $query->where('payment_only', 0);
    }

    public function ScopePayoutascorder($query)
    {
        return $query->orderBy('payout_sort_order', 'ASC');
    }

    public function ScopeRenewal($query)
    {
        return $query->where(['membership_renewal' => 1, 'status' => 1]);
    }

    public function ScopeActivePackage($query)
    {
        return $query->where('status', '1');
    }

    public function ScopeRegistration($query)
    {
        return $query->where(['registration' => 1, 'status' => 1]);
    }

    public static function checkActive($type)
    {
        return static::where('slug', $type)->where('status', 1)->exists();
    }

    public function scopeActivePayout($query)
    {
        return $query->where('payout_status', 1);
    }

    public function scopeActiveRepurchase($query)
    {
        return $query->where('repurchase', 1)->where('status', 1);
    }

    public function scopeActiveUpgrade($query)
    {
        return $query->where('status', 1)->where('upgradation', 1);
    }

    protected static function booted()
    {
        static::updated(function ($settings) {
            $activity = new Activity();
            if (auth()->user()->user_type == 'admin') {
                $guard = 'web';
                $userType = 'admin';
            } elseif (auth()->user()->user_type == 'employee') {
                $guard = 'employee';
                $userType = 'employee';
            }
            //add a feild in activity table--usertype
            $activity->user_id = auth()->user()->id;
            $activity->ip = request()->ip();
            $activity->activity = 'PaymentGateway changed';
            $activity->user_type = $userType;
            $activity->description = auth()->user()->username.' changed PaymentGateway Settings';
            $activity->data = json_encode($settings);
            $activity->save();
            $prefix = config('database.connections.mysql.prefix');
            $PaymentGatewayConfig = PaymentGatewayConfig::first();
            Cache::forever("{$prefix}PaymentGatewayConfig", $PaymentGatewayConfig);
        });
    }
    public function details()
    {
        return $this->hasOne(PaymentGatewayDetail::class, 'payment_gateway_id');
    }

}
