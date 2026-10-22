<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_number',
        'provider_id',
        'user_id',
        'purchase_date',
        'status',
        'subtotal',
        'tax',
        'total',
        'received_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'received_at'   => 'datetime',
            'subtotal'      => 'decimal:2',
            'tax'           => 'decimal:2',
            'total'         => 'decimal:2',
        ];
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_details')
            ->withPivot('quantity', 'received_quantity', 'unit_cost', 'subtotal')
            ->withTimestamps();
    }
}
