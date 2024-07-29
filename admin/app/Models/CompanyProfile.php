<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CompanyProfile extends Model
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
        static::updated(function () {
            $prefix = session()->get('prefix');
            if (Cache::has("{$prefix}_companyProfile")) {
                Cache::forget("{$prefix}_companyProfile");
            }
            $profile = CompanyProfile::first();
            Cache::forever("{$prefix}_companyProfile", $profile);
        });
    }
}
