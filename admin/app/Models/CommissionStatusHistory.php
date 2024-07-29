<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['commission', 'user_id', 'status', 'date', 'data'];

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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeInitialised($query)
    {
        return $query->where('status', 0);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 2);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 3);
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                switch ($value) {
                    case 0:
                        return 'initialized';
                        break;
                    case 1:
                        return 'processing';
                        break;
                    case 2:
                        return 'success';
                        break;
                    case 3:
                        return 'failed';
                    default:
                        return 'Na';
                        break;
                }
            }
        );
    }
}
