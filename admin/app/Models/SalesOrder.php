<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no', 'user_id', 'product_id', 'product_pv', 'amount', 'payment_method', 'pending_user_id', 'oc_product_id' ,

    ];

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

    public function package()
    {
        return $this->belongsTo(Package::class, 'product_id');
    }
    public function product()
    {
        return $this->belongsTo(OCProduct::class, 'oc_product_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentGatewayConfig::class, 'payment_method');
    }
}
