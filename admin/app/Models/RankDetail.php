<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankDetail extends Model
{
    use HasFactory;

    protected $fillable = ['rank_id', 'referral_count', 'party_comm', 'personal_pv', 'group_pv',
        'downline_package_count', 'downline_count', 'referral_commission', 'team_member_count',
        'pool_status', 'status',
    ];

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
    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    public function purchaseRank()
    {
        return $this->hasMany(PurchaseRank::class, 'rank_id');
    }

    public function donwlineRank()
    {
        return $this->hasMany(DownlineRank::class, 'rank_id');
    }

    public function rankPackage()
    {
        return $this->belongsToMany(Package::class, 'joinee_ranks', 'rank_id', 'package_id')->withTimestamps();
    }

    public function package()
    {
        return $this->belongsToMany(Package::class, 'purchase_ranks', 'rank_id', 'package_id')->withPivot('package_count')->withTimestamps();
    }

    public function joineeRank()
    {
        return $this->hasMany(JoineeRank::class, 'rank_id');
    }
}
