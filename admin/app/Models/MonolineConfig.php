<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonolineConfig extends Model
{
    protected $fillable = [
        'downline_count', 'bonus', 'referral_count', 'tree_icon'
    ];
    use HasFactory;
}
