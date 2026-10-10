<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'user_id',
        'sale_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'subtotal'  => 'decimal:2',
            'tax'       => 'decimal:2',
            'total'     => 'decimal:2',
        ];
    }

    // Relación con el Cliente / Propietario de Mascota
    public function client()
    {
        return $this->belongsTo(Owner::class, 'client_id')->withTrashed();
    }

    // Relación con el Vendedor / Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con los detalles de la venta
    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    // Relación N:M con Productos mediante la tabla pivote sale_details
    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_details')
            ->withPivot('quantity', 'unit_price', 'subtotal')
            ->withTimestamps();
    }
}