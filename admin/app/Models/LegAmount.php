<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegAmount extends Model
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
    protected function dateOfSubmission(): Attribute
    {
        return Attribute::make(

            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    protected function releasedDate(): Attribute
    {
        return Attribute::make(

            get: fn ($value) => $this->parseDateWithTimezone($value),
        );
    }
    public function userDetails()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function viewUser()
    {
        return $this->belongsToMany(User::class, 'user_id');
    }
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_id');
    }
}
