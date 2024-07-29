<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuration extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::updated(function ($compansation) {
            $prefix = config('database.connections.mysql.prefix');
            $configuration = Configuration::first();
            Cache::forever("{$prefix}configurations", $configuration);
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

    public function images()
    {
        return $this->morphMany('App\UploadImage', 'imageable');
    }

    public function image()
    {
        return $this->morphOne(UploadImage::class, 'imageable');
    }
}
