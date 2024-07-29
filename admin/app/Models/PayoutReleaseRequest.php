<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutReleaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'amount', 'balance_amount', 'status', 'read_status', 'payout_fee', 'payment_method',
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paidAmounts()
    {
        return $this->hasMany(AmountPaid::class, 'id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 2);
    }

    public function scopePaymentMethod($query, $paymentMethod)
    {
        return $query->where('payment_method', $paymentMethod);
    }

    public function paymentMethod()
    {
        return $this->hasOne(PaymentGatewayConfig::class, 'id', 'payment_method');
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
