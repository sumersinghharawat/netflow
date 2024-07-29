<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaypalPayoutBatchDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id', 'user_id', 'response_data', 'payout_data', 'webhook_data', 'status', 'paypal_data', 'reference_id', 'batch_status',
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

    public function reference()
    {
        return $this->belongsTo(AmountPaid::class, 'reference_id');
    }
}
