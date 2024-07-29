<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treepath extends Model
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

    public function ancestor()
    {
        return $this->belongsTo(User::class, 'ancestor', 'id');
    }

    public function descendant()
    {
        return $this->belongsTo(User::class, 'descendant', 'id');
    }
    public function childDetail()
    {
        return $this->belongsTo(User::class, 'descendant', 'id');
    }

    // public function ancestors()
    // {
    //     return $this->belongsToMany(User::class, 'treepaths', 'descendant', 'ancestor')->withTimestamps();
    // }
}
