<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SignupSetting extends Model
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
        static::updated(function ($settings) {
            $activity = new Activity();
            if (auth()->user()->user_type == 'admin') {
                $guard = 'web';
                $userType = 'admin';
            } elseif (auth()->user()->user_type == 'employee') {
                $guard = 'employee';
                $userType = 'employee';
            }
            //add a feild in activity table--usertype
            $activity->user_id = auth()->user()->id;
            $activity->ip = request()->ip();
            $activity->activity = 'Signup Settings changed';
            $activity->user_type = $userType;
            $activity->description = auth()->user()->username.' changed signupsettings';
            $activity->data = json_encode($settings);
            $activity->save();
            $prefix = config('database.connections.mysql.prefix');
            $siginupSettings = SignupSetting::first();
            Cache::forever("{$prefix}signupSettings", $siginupSettings);
        });
    }
}
