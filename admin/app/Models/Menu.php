<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::updated(function ($menuitems) {
            $prefix = config('database.connections.mysql.prefix');
            if (Cache::has("{$prefix}menuitems")) {
                Cache::forget("{$prefix}menuitems");
            }
            $menuitems = Menu::with('children.permission', 'permission')->has('permission', '=', 1)->where('react_only', 0)->where('side_menu', 1)->orderBy('order')->get();
            Cache::forever("{$prefix}menuitems", $menuitems);
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

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    public function parent()
    {
        return $this->hasOne(Menu::class, 'id', 'parent_id');
    }

    public function permission(): HasOne
    {
        return $this->hasOne(MenuPermission::class);
    }
    public function employeePermission(): HasOne
    {
        return $this->hasOne(EmployeeMenu::class, 'menu_id');
    }
}
