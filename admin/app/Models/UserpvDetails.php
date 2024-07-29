<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserpvDetails extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_pv', 'total_gpv'];
}
