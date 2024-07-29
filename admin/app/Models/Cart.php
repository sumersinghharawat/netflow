<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cart extends Model
{
    use HasFactory;

    protected $guarded;
    // use UUID;

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
        return $this->belongsToMany(Package::class, 'carts', 'user_id', 'package_id')->withPivot('quantity')->withTimestamps();
    }

    public function ScopeUser($query)
    {
        return $query->where('user_id', Auth::user()->id);
    }

    public function packageDetails()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
}
