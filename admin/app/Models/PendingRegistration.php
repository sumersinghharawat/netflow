<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PendingRegistration extends Model
{
    use HasFactory;

    protected $guarded = [];
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

    protected function username(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => str_replace( " " , "_" , strtolower($value)),
        );
    }
    public function RegistraionPackage()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function sponsorData()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function ScopePendingRegDet($query, $ids = null)
    {
        $data = $query->where('status', 'pending');

        return ($ids) ? $data->whereIn('id', $ids) : $data;
    }

    public function bankReciept()
    {
        return $this->hasOne(PaymentReceipt::class, 'pending_registrations_id');
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGatewayConfig::class, 'payment_method');
    }
}
