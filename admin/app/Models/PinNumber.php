<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinNumber extends Model
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

        protected function allocDate(): Attribute
        {
            return Attribute::make(
                
                get: fn ($value) => $this->parseDateWithTimezone($value),
            );
        }
    
        protected function uploadedDate(): Attribute
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
    protected static function booted()
    {
        static::created(function ($epin) {
            $activity = new Activity();
            $activity->user_id = auth()->user()->id;
            $activity->ip = request()->ip();
            $activity->activity = 'epin generated';
            $activity->description = auth()->user()->username.'epin generated';
            $activity->data = json_encode($epin);
            $activity->user_type = auth()->user()->user_type;
            $activity->save();
        });

        static::updated(function ($epin) {
            $activity = new Activity();
            $activity->user_id = auth()->user()->id;
            $activity->ip = request()->ip();
            $activity->activity = 'epin modified';
            $activity->description = auth()->user()->username.'epin modified';
            $activity->data = json_encode($epin);
            $activity->user_type = auth()->user()->user_type;
            $activity->save();
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeNonExpired($query)
    {
        return $query->where('expiry_date', '>=', now());
    }

    public function allocatedUser()
    {
        return $this->belongsTo(User::class, 'allocated_user');
    }

    public function scopeActivePurchaseStatus($query)
    {
        return $query->where('purchase_status', true);
    }

    public function scopeAllocateUser($query, $user_id)
    {
        return $query->where('allocated_user', $user_id);
    }

    public function scopeLike($query, $string)
    {
        return $query->where('numbers', 'like', '%'.$string.'%');
    }

    public function scopecheckEpins($query, $epins)
    {
        $query->whereIn('numbers', [...$epins]);
    }
}
