<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'price', 'stock', 'reorder_level', 'status'];

    protected $casts = ['price' => 'decimal:2', 'stock' => 'integer', 'reorder_level' => 'integer'];
}
