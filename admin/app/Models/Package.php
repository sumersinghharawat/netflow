<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'product_id', 'price', 'bv_value', 'pair_value', 'quantity', 'referral_commission', 'pair_price',
        'roi', 'description', 'days', 'validity', 'joinee_commission', 'category_id', 'tree_icon', 'active', 'image', 'reentry_limit'
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

    public function repurchasecategory()
    {
        return $this->hasMany(Repurchasecategory::class);
    }

    // public function category()
    // {
    //     return $this->belongsTo(Repurchasecategory::class);
    // }
    public function levelCommissionRegisterPack()
    {
        return $this->hasMany(
            LevelCommissionRegisterPack::class,
            'package_id',
        );
    }
    // public function levelCommissionRegisterPackWithLevel($level)
    // {
    //     return $this->hasMany(
    //         LevelCommissionRegisterPack::class,
    //         'package_id',
    //     )->where('level', $level);
    // }

    public function purchaseRank()
    {
        return $this->hasMany(PurchaseRank::class);
    }

    public function joineeRank()
    {
        return $this->belongsToMany(RankDetail::class, 'joinee_ranks', 'package_id', 'rank_id');
    }

    public function rankDetail()
    {
        return $this->belongsToMany(RankDetail::class, 'purchase_ranks', 'package_id', 'rank_id')->withPivot('package_count');
    }

    public function ScopeActivePackage($query)
    {
        return $query->where('active', 1);
    }

    public function ScopeAscOrderById($query)
    {
        return $query->orderBY('id', 'ASC');
    }

    public function ScopeActiveRegPackage($query)
    {
        return $query->where('active', 1)->where('type', 'registration');
    }

    public function ScopeRegistrationPack($query, $id = null)
    {
        $data = $query->where('type', 'registration');

        return ($id) ? $data->where('id', $id) : $data;
    }

    public function ScopeRepurchase($query)
    {
        return $query->where('type', 'repurchase');
    }

    public function ScopeActiveRepurchasePackage($query)
    {
        return $query->where('active', 1)->where('type', 'repurchase');
    }

    public function ScopeBlockedRepurchasePackage($query)
    {
        return $query->where('active', false)->where('type', 'repurchase');
    }

    public function getUser()
    {
        return $this->hasMany(User::class, 'product_id', 'id');
    }

    public function matchingCommission()
    {
        return $this->hasOne(MatchingCommission::class, 'package_id');
    }

    public function repurchaseSales()
    {
        return $this->hasMany(SalesCommission::class, 'package_id');
    }

    public function ScopeBlockRegPackage($query)
    {
        return $query->where('active', 0)->where('type', 'registration');
    }

    public function category()
    {
        return $this->hasOne(RepurchaseCategory::class, 'id', 'category_id');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'product_id');
    }
    public function usersRegistration()
    {
        return $this->hasMany(UsersRegistration::class, 'product_id');
    }
    public function stripe()
    {
        return $this->hasOne(StripeProducts::class,'product_id');
    }
    public function paypal()
    {
        return $this->hasOne(PaypalProducts::class,'product_id');
    }
    public function rank()
    {
        return $this->hasOne(Rank::class);
    }
}
