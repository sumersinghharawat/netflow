<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UsersRegistration extends Model
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


    protected function username(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => str_replace( " " , "_" , strtolower($value)),
        );
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGatewayConfig::class, 'payment_method');
    }

    public function RegistrationPackage()
    {
        return $this->belongsTo(Package::class, 'product_id');
    }
    public function package()
    {
        $moduleStatus = ModuleStatus::first();
        if ($moduleStatus->ecom_status || $moduleStatus->ecom_demo_status) {
            return $this->belongsTo(OCProduct::class, 'oc_product_id');
        }

        return $this->belongsTo(Package::class, 'product_id');
    }
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class, 'user_id');
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
