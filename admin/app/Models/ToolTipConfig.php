<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ToolTipConfig extends Model
{
    use HasFactory;

    protected $guarded;

    protected $table = 'tooltips_config';

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
        static::updated(function ($data) {
            $activity = new Activity();
            if (auth()->user()->user_type == 'admin') {
                $guard = 'web';
                $userType = 'admin';
            } elseif (auth()->user()->user_type == 'employee') {
                $guard = 'employee';
                $userType = 'employee';
            }
            $activity->user_id = auth()->user()->id;
            $activity->ip = request()->ip();
            $activity->activity = 'TreeTooltip config';
            $activity->user_type = $userType;
            $activity->description = auth()->user()->username.' changed TreeTooltip config';
            $activity->data = json_encode($data);
            $activity->save();
            $prefix = config('database.connections.mysql.prefix');
            $ToolTipConfig = ToolTipConfig::first();
            Cache::forever("{$prefix}ToolTipConfig", $ToolTipConfig);
        });
    }

    public function ScopeActive($query)
    {
        return $query->where('status', 1);
    }
}
