<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReentryRelation extends Model
{
    use HasFactory;

    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function parentDetail()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
