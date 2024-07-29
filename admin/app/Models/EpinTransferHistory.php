<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpinTransferHistory extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($epinTransferHistory) {
            $activity = new Activity();
            $activity->user_id = auth()->user()->id;
            $activity->ip = request()->ip();
            $activity->activity = 'E-pin Transfered';
            $activity->description = auth()->user()->username.'E-pin Transfered';
            $activity->data = json_encode($epinTransferHistory);
            $activity->user_type = auth()->user()->user_type;
            $activity->save();
        });
    }

    protected $fillable = [
        'to_user', 'from_user', 'epin_id', 'ip', 'done_by', 'date', 'activity',
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
    protected function date(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user');
    }

    public function epin()
    {
        return $this->belongsTo(PinNumber::class, 'epin_id');
    }
}
