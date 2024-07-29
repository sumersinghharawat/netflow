<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoineeRank extends Model
{
    use HasFactory;

    protected $guarded;

    public function ranks()
    {
        return $this->belongsTo(Rank::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
