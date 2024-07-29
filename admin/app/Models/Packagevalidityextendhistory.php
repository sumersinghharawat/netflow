<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packagevalidityextendhistory extends Model
{
    use HasFactory;

    protected $table = 'package_validity_extend_histories';

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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package()
    {
        $moduleStatus = ModuleStatus::first();
        if ($moduleStatus->ecom_status) {
            return $this->belongsTo(OCProduct::class, 'oc_product_id');
        }
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function bankReciept()
    {
        return $this->hasOne(PaymentReceipt::class, 'user_id', 'user_id');
    }
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentGatewayConfig::class, 'payment_type');
    }
}
