<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenelogyLevelCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'level', 'percentage',
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
    public function scopeLevelOrder($query)
    {
        return $query->orderBy('level', 'ASC');
    }
}