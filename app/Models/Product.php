<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'barcode', 'name', 'sale_price', 'current_stock', 'minimum_stock', 'active'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
