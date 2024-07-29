<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'requested_pin_count', 'allotted_pin_count', 'requested_date', 'status', 'remarks', 'pin_amount', 'read_status',
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
    protected function requestedDate(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }

    protected function expiryDate(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    public function requestedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
