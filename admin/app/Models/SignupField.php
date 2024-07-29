<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignupField extends Model
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
    public function ScopeSortAscOrder($query)
    {
        return $query->orderBy('sort_order', 'ASC');
    }

    public function ScopeSortDscOrder($query)
    {
        return $query->orderBy('sort_order', 'DESC');
    }

    public function ScopeActive($query)
    {
        return $this->where(['status' => 1]);
    }

    public function ScopeMandatoryFields($query)
    {
        return $this->where(['required' => 1]);
    }
    public function ScopeCustom($query)
    {
        return $this->where(['is_custom' => 1]);
    }
    public function ScopeNotCustom($query)
    {
        return $this->where(['is_custom' => 0]);
    }
    public function ScopeActiveCustom($query)
    {
        return $this->where(['is_custom' => 1, 'status' => 1]);
    }

    public function customFieldLang()
    {
        return $this->hasMany(CustomfieldLang::class, 'customfield_id');
    }
}
