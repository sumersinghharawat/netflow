<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Mailsetting extends Model
{
    use HasFactory;
    protected $table = 'mail_settings';
    protected $fillable = [
                'smtp_authentication', 'from_name', 'from_email', 'smtp_host', 'smtp_username',
                'smtp_password', 'smtp_port', 'smtp_timeout', 'reg_mailstatus', 'reg_mailcontent',
                'reg_mailtype', 'smtp_authentication', 'smtp_protocol'
            ];

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
            $activity->activity = 'Mailsettings changed';
            $activity->user_type = $userType;
            $activity->description = auth()->user()->username.' changed MailSettings';
            $activity->data = json_encode($settings);
            $activity->save();
            $prefix = config('database.connections.mysql.prefix');
            $Mailsetting = Mailsetting::first();
            Cache::forever("{$prefix}Mailsetting", $Mailsetting);
        });
    }
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
}
