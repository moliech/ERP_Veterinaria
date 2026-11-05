<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    public const CONCEPTS = [
        'inventario_inicial' => 'Inventario Inicial',
        'compra'             => 'Compra a Proveedor',
        'venta'              => 'Venta en Mostrador',
        'anulacion_venta'    => 'Anulación de Venta',
        'ajuste'             => 'Ajuste Físico',
    ];

    protected $fillable = [
        'product_id',
        'type',
        'concept',
        'quantity',
        'balance',
        'unit_cost',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'  => 'integer',
            'balance'   => 'integer',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function conceptLabel(): string
    {
        return self::CONCEPTS[$this->concept] ?? $this->concept;
    }

    public function referenceLabel(): ?string
    {
        return $this->reference?->invoice_number ?? $this->reference?->purchase_number;
    }
}
