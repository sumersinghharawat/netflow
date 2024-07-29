<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinAmountDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

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
    public function scopeAscOrder($query)
    {
        return $query->orderBy('amount', 'ASC');
    }

    public function scopeGetIds($query, $ids = null)
    {
        return ($ids) ? $query->whereIn('id', $ids) : [];
    }

    public function scopeLike($query, $string)
    {
        return $query->where('amount', 'like', '%'.$string.'%');
    }
}
