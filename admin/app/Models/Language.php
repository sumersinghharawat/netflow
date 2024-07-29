<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
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
    public function scopeActive($query)
    {
        $moduleStatus = ModuleStatus::select('multilang_status', 'id')->first();
        if (!$moduleStatus->multilang_status) {
            return $query->where('default', true);
        }
        return $query->where('status', 1);
    }
    public function scopeDefault($query)
    {
        return $query->where('default', 1);
    }
    public function checkIsDefault(): int
    {
        return $this->default;
    }
}
