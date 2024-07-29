<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OCProduct extends Model
{
    use HasFactory;

    protected $table = 'oc_product';

    protected $primaryKey = 'product_id';

    protected $guarded = [];

    public $timestamps = false;
    protected function parseDateWithTimezone($value): Carbon
    {
        return Carbon::parse($value)->timezone(config('mlm.local_timezone'));
    }
    
    protected function dateAvailable(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }

    protected function dateAdded(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    protected function dateModified(): Attribute
    {
        return Attribute::make(
            
            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    public function getUser()
    {
        return $this->hasMany(User::class, 'product_id', 'product_id');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'oc_product_id');
    }

    public function usersRegistration()
    {
        return $this->hasMany(UsersRegistration::class, 'oc_product_id');
    }

    public function levelCommissionRegisterPack()
    {
         return $this->hasMany(
            LevelCommissionRegisterPack::class,
            'oc_product_id',
            'id',
        );
    }
    public function ScopeActiveRegProduct()
    {
        return $this->where('package_type', 'registration')->where('status', 1)->get();
    }
}
