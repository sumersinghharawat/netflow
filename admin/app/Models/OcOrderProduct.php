<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcOrderProduct extends Model
{
    use HasFactory;

    protected $table = 'oc_order_product';

    protected $primaryKey = 'order_product_id';

    protected $guarded = [];

    public $timestamps = false;


}
