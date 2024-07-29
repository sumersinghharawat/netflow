<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded;

    protected $fillable = [
        'invoice_no', 'user_id', 'order_address_id', 'order_date', 'total_amount', 'total_pv', 'order_status', 'payment_method',
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
    protected function orderDate(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ScopePendingOrderDet($query, $ids = null)
    {
        $data = $query->where('order_status', '1');

        return ($ids) ? $data->whereIn('id', $ids) : $data;
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function userDetail()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }

    public function scopeUserSpecified($query, $user)
    {
        return $query->where('user_id', $user);
    }

    public function scopePending($query)
    {
        return $query->where('order_status', '0');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentGatewayConfig::class, 'payment_method');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'order_address_id');
    }
    public function userDetails()
    {
        return $this->hasOneThrough(
            'App\Models\UserDetail', // The related model you want to retrieve
            'App\Models\User', // The intermediate model that links the amount table to the user_details table
            'id', // The foreign key on the users table that links to the amounts table
            'user_id', // The foreign key on the user_details table that links to the users table
            'user_id', // The local key on the amounts table
            'id' // The local key on the users table
        );
    }
}
