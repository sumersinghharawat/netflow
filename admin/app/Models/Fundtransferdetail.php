<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fundtransferdetail extends Model
{
    use HasFactory;

    protected $table = 'fund_transfer_details';

    protected $fillable = ['from_id', 'to_id', 'amount', 'notes', 'amount_type', 'trans_fee', 'transaction_id'];
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
    public function user()
    {
        return $this->belongsTo(User::class, 'from_id');
    }
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_id');
    }
    public function userDetails()
    {
        return $this->hasOneThrough(
            'App\Models\UserDetail', // The related model you want to retrieve
            'App\Models\User', // The intermediate model that links the amount table to the user_details table
            'id', // The foreign key on the users table that links to the amounts table
            'user_id', // The foreign key on the user_details table that links to the users table
            'user_id', // The local key on the amounts table
            'id' // The local key on the users table
        );
    }
}
