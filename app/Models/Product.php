<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'barcode',
        'name',
        'cost_price',
        'sale_price',
        'tax_rate',
        'current_stock',
        'minimum_stock',
        'batch_code',
        'expiration_date',
        'active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'expiration_date' => 'date',
        'active' => 'boolean',
    ];

    public static function availableTaxRates(): array
    {
        return [
            '19.00' => '19% — Tarifa General',
            '5.00' => '5% — Tarifa Diferencial (Alimentos / Insumos)',
            '0.00' => '0% — Exento / Excluido de IVA',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function getStockAttribute(): int
    {
        return (int) $this->current_stock;
    }

    public function profitMargin(): float
    {
        $cost = (float) $this->cost_price;
        $sale = (float) $this->sale_price;

        if ($cost <= 0) {
            return $sale > 0 ? 100.0 : 0.0;
        }

        return round((($sale - $cost) / $cost) * 100, 1);
    }

    public function priceWithTax(): float
    {
        $sale = (float) $this->sale_price;
        $rate = (float) ($this->tax_rate ?? 19);

        return round($sale * (1 + ($rate / 100)), 2);
    }

    public function expirationStatus(): array
    {
        if (! $this->expiration_date) {
            return [
                'label' => 'No perecedero',
                'badge' => 'bg-slate-100 text-slate-600 border-slate-200',
            ];
        }

        $daysLeft = (int) now()->startOfDay()->diffInDays($this->expiration_date->startOfDay(), false);

        if ($daysLeft < 0) {
            return [
                'label' => 'Lote vencido',
                'badge' => 'bg-red-100 text-red-800 border-red-300',
            ];
        }

        if ($daysLeft <= 60) {
            return [
                'label' => 'Próximo a vencer (' . $daysLeft . 'd)',
                'badge' => 'bg-amber-100 text-amber-800 border-amber-300',
            ];
        }

        return [
            'label' => 'Lote vigente',
            'badge' => 'bg-green-100 text-green-800 border-green-300',
        ];
    }
}
