<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductStock extends Pivot
{
    protected $table = 'product_stock';

    protected $fillable = ['product_id', 'stock_location_id', 'quantity'];
}

