<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username', 'user_type', 'password', 'product_id', 'sponsor_id',
        'date_of_joining', 'position', 'father_id', 'product_validity',
        'user_level', 'sponsor_level', 'register_by_using', 'personal_pv', 'group_pv',
        'binary_leg', 'user_rank_id', 'active', 'oc_product_id', 'ecom_customer_ref_id', 'is_reentry_user','email', 'sponsor_index'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'cart.pivot.id' => 'string',
    ];
    protected function parseDateWithTimezone($value): Carbon
    {
        return Carbon::parse($value)->timezone(config('mlm.local_timezone'));
    }
    protected function dateOfJoining(): Attribute
    {
        return Attribute::make(

            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }

    protected function productValidity(): Attribute
    {
        return Attribute::make(

            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
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

    protected function username(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => str_replace( " " , "_" , strtolower($value)),
        );
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class, 'user_id');
    }

    public function employeeDetail()
    {
        return $this->hasOne(EmployeeDetail::class, 'employee_id');
    }

    public function transPassword()
    {
        return $this->hasOne(TransactionPassword::class);
    }

    public function legDetails()
    {
        return $this->hasOne(LegDetail::class);
    }

    public function legamountDetails()
    {
        return $this->hasOne(LegAmount::class);
    }

    public function legamtDetails()
    {
        return $this->hasMany(LegAmount::class, 'user_id')->orderBy('total_amount', 'DESC');
    }

    public function userDetails()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function userRegDetails()
    {
        return $this->hasOne(UsersRegistration::class, 'user_id');
    }

    public function scopeFatherId($query, $user_id)
    {
        return $query->where('father_id', $user_id);
    }

    public function scopeGetAdmin($query)
    {
        return $query->where('user_type', 'admin')->first();
    }

    public function scopeNotGetAdmin($query)
    {
        return $query->where('user_type', '!=', 'admin');
    }

    public function scopeActiveUsers($query)
    {
        return $query->where('user_type', 'user')->where('active', true)->orderBy('date_of_joining', 'DESC')->limit(10);
    }

    public function scopeGetUsers($query)
    {
        return $query->where('user_type', 'user')->get();
    }

    public function scopeGetEmployees($query)
    {
        return $query->where('user_type', 'employee')->where('active', 1)->get();
    }

    public function ancestors()
    {
        return $this->belongsToMany(User::class, 'treepaths', 'descendant', 'ancestor')->withTimestamps()->withPivot('depth');
    }

    public function descendants()
    {
        return $this->belongsToMany(User::class, 'treepaths', 'ancestor', 'descendant')->withTimestamps();
    }

    public function rankDetail()
    {
        return $this->belongsTo(Rank::class, 'user_rank_id', 'id');
    }

    public function rankHistory()
    {
        return $this->belongsToMany(Rank::class, 'rank_users');
    }

    public function LegAmount()
    {
        return $this->belongsTo(LegAmount::class, 'id', 'user_id')->orderBy('total_amount');
    }

    public function userBalance()
    {
        return $this->hasOne(UserBalanceAmount::class);
    }

    public function scopeLike($query, $string)
    {
        return $query->where('username', 'like', '%' . $string . '%');
    }

    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function cart()
    {
        return $this->belongsToMany(User::class, 'carts', 'user_id', 'package_id')->withPivot('quantity')->withTimestamps();
    }

    public function rank()
    {
        return $this->belongsToMany(Rank::class, 'rank_users', 'user_id', 'rank_id')->withPivot('status', 'join_date')->withTimestamps();
    }

    public function sponsorAncestors()
    {
        return $this->belongsToMany(User::class, 'sponsor_treepaths', 'descendant', 'ancestor')->withTimestamps();
    }

    public function descendant()
    {
        return $this->belongsToMany(User::class, 'treepaths', 'ancestor', 'descendant')->withTimestamps()->withPivot('depth');
    }

    public function getDesc()
    {
        return $this->belongsToMany(Treepath::class, 'descendant', 'id');
    }

    public function sponsorDescendant()
    {
        return $this->belongsToMany(User::class, 'sponsor_treepaths', 'ancestor', 'descendant')->withTimestamps();
    }

    public function children()
    {
        return $this->hasMany(User::class, 'father_id')->whereIn('user_type', ['admin', 'user']);
    }

    public function package()
    {
        $moduleStatus = ModuleStatus::first();
        if ($moduleStatus->ecom_status || $moduleStatus->ecom_demo_status) {
            return $this->belongsTo(OCProduct::class, 'oc_product_id');
        }

        return $this->belongsTo(Package::class, 'product_id');
    }

    public function fatherDetails()
    {
        return $this->belongsTo(User::class, 'father_id');
    }

    public function ewalletTransfer()
    {
        return $this->hasMany(EwalletTransferHistory::class);
    }

    public function inbox()
    {
        return $this->hasMany(MailBox::class, 'to_user_id');
    }

    public function locale()
    {
        return $this->belongsTo(Language::class, 'default_lang');
    }

    public function currency()
    {
        return $this->belongsTo(CurrencyDetail::class, 'default_currency');
    }

    public function repurchaseOrder()
    {
        return $this->hasMany(Order::class);
    }

    public function ScopeActiveUser($query)
    {
        return $query->where('active', true)->first();
    }

    public function ranks()
    {
        return $this->hasOne(Rank::class, 'id', 'user_rank_id');
    }

    public function donationLevel()
    {
        return $this->belongsToMany(DonationRate::class, 'donation_levels', 'user', 'level')->withTimestamps();
    }

    public function userUpgradeHistoryLevel()
    {
        return $this->belongsToMany(DonationRate::class, 'user_upgrade_histories', 'user', 'level')->withTimestamps();
    }

    public function checkPackageUpgradeAvailable()
    {
        $currentPackage = $this->package;
        $moduleStatus = ModuleStatus::first();
        if ($moduleStatus->ecom_status || $moduleStatus->ecom_demo_status) {
            $newPackage = OCProduct::where('product_id', '!=', $currentPackage->product_id)
                ->where('price', '>=', $currentPackage->price)->orderBy('price', 'ASC')->where('status', 1)->first();

            return ($newPackage) ? $newPackage : false;
        }
        $newPackage = Package::where('id', '!=', $currentPackage->id)
            ->where('price', '>=', $currentPackage->price)
            ->ActiveRegPackage()->orderBy('price', 'ASC')->first();

        return ($newPackage) ? $newPackage : false;
    }

    public function contacts()
    {
        return $this->hasMany(Contacts::class, 'owner_id');
    }

    public function kycDocs()
    {
        return $this->hasMany(KycDocs::class, 'user_id');
    }

    public function pvDetails(): HasOne
    {
        return $this->hasOne(UserpvDetails::class);
    }

    public function step()
    {
        return $this->hasOne(StairStep::class);
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'sponsor_id');
    }

    public function employeeMenu()
    {
        return $this->belongsToMany(Menu::class, 'employee_menus', 'employee_id', 'menu_id')->withPivot('is_heading', 'has_children', 'is_child')->withTimestamps();
    }

    public function employeeDashboard()
    {
        return $this->belongsToMany(Employee::class, 'employee_dashboards', 'employee_id', 'dashboard_id')->withTimestamps();
    }

    public function empDashboard()
    {
        return $this->belongsToMany(EmployeeDashboardItem::class, 'employee_dashboard_permissions', 'employee_id', 'dashboard_id')->withTimestamps();
    }
    public function hosts()
    {
        return $this->hasMany(PartyHost::class, 'added_by');
    }
    public function ewalletFrom()
    {
        return $this->hasMany(EwalletHistory::class, 'from_id');
    }
    public function additionalDetails()
    {
        return $this->hasMany(CustomfieldValues::class, 'user_id');
    }
    public function salesOrder()
    {
        return $this->hasOne(SalesOrder::class, 'user_id');
    }
    public function downlines()
    {
        return $this->hasMany(Treepath::class, 'ancestor', 'id');
    }
    public function downlinesAll()
    {
        return $this->downlines()->with(__FUNCTION__, 'downlines');
    }
    public function closureChildren()
    {
        return $this->hasMany(Treepath::class, 'ancestor');
    }
    public function legAmounts()
    {
        return $this->hasMany(LegAmount::class, 'user_id');
    }
    public function closureSponsorChildren()
    {
        return $this->hasMany(SponsorTreepath::class, 'ancestor');
    }


    // for dashboard tabs query like top earners
    public function topEarners()
    {
        return $this->belongsTo(LegAmount::class, 'id', 'user_id');
    }
    public function nextReentry()
    {
        return $this->hasOne(UserReentry::class, 'user_id');
    }

    public function scopeUsers($query)
    {
        return $query->whereIn('user_type', ['user', 'admin']);
    }
    public function reentries()
    {
        return $this->hasMany(User::class, 'sponsor_id')->where('user_type', 'reentry');
    }
    public function reentryParent()
    {
        return $this->hasOne(ReentryRelation::class, 'user_id');
    }
    public function ocOrder()
    {
        return $this->hasOne(OcOrder::class, 'customer_id' , 'ecom_customer_ref_id');
    }
    public function placementData() {
        return $this->hasOne(UserPlacement::class);
    }
    public function Aggrigate()
    {
        return $this->belongsTo(AggregateUserCommissionAndIncome::class, 'id', 'user_id');
    }


}
