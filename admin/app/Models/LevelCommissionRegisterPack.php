<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelCommissionRegisterPack extends Model
{
    use HasFactory;

    protected $fillable = ['level', 'package_id', 'commission', 'percentage', 'oc_product_id'];

    protected static function booted()
    {
        static::updated(function ($data) {
            if (auth()->user()->user_type == 'admin') {
                $activity = new Activity();
            } elseif (auth()->user()->user_type == 'employee') {
                $activity = new EmployeeActivity();
            }
            $activity->user_id = auth()->user()->id;
            $activity->ip = request()->ip();
            $activity->activity = 'level commission';
            $activity->description = auth()->user()->username.' changed level commission config';
            $activity->data = json_encode($data);
            $activity->save();
        });

        static::deleted(function ($data) {
            if (auth()->user()->user_type == 'admin') {
                $activity = new Activity();
            } elseif (auth()->user()->user_type == 'employee') {
                $activity = new EmployeeActivity();
            }
            $activity->user_id = auth()->user()->id;
            $activity->ip = request()->ip();
            $activity->activity = 'level commission level deleted';
            $activity->description = auth()->user()->username.' deleted level commission config';
            $activity->data = json_encode($data);
            $activity->save();
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
    
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function ScopeLevelAscOrder($query)
    {
        return $query->orderBy('level', 'ASC');
    }
}
