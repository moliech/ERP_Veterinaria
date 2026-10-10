<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['category_id', 'barcode', 'name', 'sale_price', 'current_stock', 'minimum_stock', 'active'];

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }
}
