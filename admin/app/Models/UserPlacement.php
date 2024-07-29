<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlacement extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['user_id', 'branch_parent', 'left_most', 'right_most'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function branchParent() {
        return $this->belongsTo(User::class, 'branch_parent');
    }
    public function leftMost() {
        return $this->belongsTo(User::class,'left_most');
    }
    public function rightMost() {
        return $this->belongsTo(User::class,'right_most');
    }
}
