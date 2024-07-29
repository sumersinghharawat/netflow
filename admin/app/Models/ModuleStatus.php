<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ModuleStatus extends Model
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
        static::updated(function ($modulestatus) {
            $prefix = config('database.connections.mysql.prefix');
            if (Cache::has("{$prefix}moduleStatus")) {
                Cache::forget("{$prefix}moduleStatus");
            }
            $modulestatus = ModuleStatus::first();
            Cache::forever("{$prefix}moduleStatus", $modulestatus);
        });
    }
}
