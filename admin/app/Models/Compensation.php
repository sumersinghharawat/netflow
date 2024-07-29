<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Compensation extends Model
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
    protected static function booted()
    {
        static::updated(function ($compansation) {
            // if(auth()->guard('web')->check()){
            //     $activity   = new Activity();
            // } elseif (auth()->guard('employee')->check()) {
            //     $activity   = new EmployeeActivity();
            // }
            // $activity->user_id      = auth()->user()->id;
            // $activity->ip           = request()->ip();
            // $activity->activity     = 'compensation on/off';
            // $activity->description  = auth()->user()->username.' changed compensation';
            // $activity->data         = json_encode($compansation);
            // $activity->save();
            $prefix = config('database.connections.mysql.prefix');
            $compansation = Compensation::first();
            Cache::forever("{$prefix}compensation", $compansation);
        });
    }
}
