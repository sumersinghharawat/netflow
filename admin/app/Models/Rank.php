<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Http\Controllers\CoreInfController as inf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Rank extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color', 'image', 'tree_icon', 'commission', 'package_id', 'oc_product_id', 'status'];

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
    protected static function booted()
    {
        static::updated(function ($data) {
            $activity = new Activity();
            if (auth()->user()->user_type == 'admin') {
                $guard = 'web';
                $userType = 'admin';
            } elseif (auth()->user()->user_type == 'employee') {
                $guard = 'employee';
                $userType = 'employee';
            }
            $activity->user_id = auth()->user()->id;
            $activity->ip = request()->ip();
            $activity->activity = 'Rank';
            $activity->user_type = $userType;
            $activity->description = auth()->user()->username.' changed Rank settings';
            $activity->data = json_encode($data);
            $activity->user_type = auth()->user()->user_type;
            $activity->save();
            $prefix = config('database.connections.mysql.prefix');
            $Rank = Rank::first();
            Cache::forever("{$prefix}Rank", $Rank);
        });
    }

    public function rankDetails()
    {
        return $this->hasOne(RankDetail::class);
    }

    public function rankCriteria()
    {
        $rankConfig = RankConfiguration::Active()->get();
        $coreInf = new inf;
        $moduleStatus = $coreInf->moduleStatus();
        if ($rankConfig->contains('slug', 'joiner-package')) {
            if ($moduleStatus->ecom_status) {
                return $this->belongsTo(OCProduct::class, 'oc_product_id', 'product_id');
            }

            return $this->belongsTo(Package::class, 'package_id');
        } else {
            return $this->hasOne(RankDetail::class, 'rank_id');
        }
    }

    public function downlineRankCount()
    {
        return $this->belongsToMany(Rank::class, 'rank_downline_rank', 'rank_id', 'downline_rank_id')->withPivot('count')->withTimestamps();
    }

    public function downinePackCount()
    {
        return $this->belongsToMany(Package::class, 'purchase_ranks', 'rank_id', 'package_id')->withPivot('count')->withTimestamps();
    }
    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value == 1) ? "Active" : "Disabled",
            set: fn ($value) => ($value == 'Active') ? 1 : 0,
        );
    }

    public function scopeActive($query)
    {
        $query->where('status', '1');
    }

    public function salesRank()
    {
        return $this->hasMany(SalesRankCommission::class, 'rank_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'rank_users');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'user_rank_id');
    }
}
