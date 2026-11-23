<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Enumerable
    {
        return Product::with('category')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Código de Barras',
            'Nombre del Producto',
            'Categoría',
            'Precio Costo',
            'Precio Base Venta',
            'Tarifa IVA (%)',
            'Stock Actual',
            'Stock Mínimo',
            'Estado',
            'Lote',
            'Fecha Vencimiento',
        ];
    }

    /**
     * @param  Product  $product
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->barcode ?? 'N/A',
            $product->name,
            $product->category?->name ?? 'Sin categoría',
            number_format((float) $product->cost_price, 2, '.', ''),
            number_format((float) $product->sale_price, 2, '.', ''),
            number_format((float) ($product->tax_rate ?? 19), 0, '.', '').'%',
            (int) $product->current_stock,
            (int) ($product->minimum_stock ?? 0),
            $product->active ? 'Activo' : 'Inactivo',
            $product->batch_code ?? 'N/A',
            $product->expiration_date ? $product->expiration_date->format('Y-m-d') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0F766E'],
                ],
            ],
        ];
    }
}
