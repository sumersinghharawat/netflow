<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycDocs extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'file_name', 'type', 'status', 'reason', 'date'];
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
    protected function date(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    public function KycCategory()
    {
        return $this->belongsTo(KycCategory::class, 'type');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', '2');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', '1');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', '0');
    }
}
