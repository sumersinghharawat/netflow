<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcOrder extends Model
{
    use HasFactory;

    protected $guraded = [];

    protected $table = 'oc_order';

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_status_id'
    ];

    public $timestamps = false;
    
    protected function parseDateWithTimezone($value): Carbon
    {
        return Carbon::parse($value)->timezone(config('mlm.local_timezone'));
    }
    
    protected function dateAdded(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    protected function dateModified(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    protected function orderDate(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    public function customer()
    {
        return $this->hasOne(OcCustomer::class, 'customer_id', 'customer_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id', 'ecom_customer_ref_id');
    }
    public function orderDetails()
    {
        return $this->hasMany(OcOrderProduct::class, 'order_id');
    }


}
