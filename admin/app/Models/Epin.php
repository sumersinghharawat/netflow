<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epin extends Model
{
    use HasFactory;

    protected $guarded;

    public function ScopeActiveEpin($query)
    {
        return $query->where('active', 'yes');
    }
}
