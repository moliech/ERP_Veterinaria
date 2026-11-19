<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
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
            'Precio Venta (con IVA 19%)',
            'Stock Actual',
            'Stock Mínimo',
            'Estado',
            'Lote',
            'Fecha Vencimiento',
        ];
    }

    /**
     * @param Product $product
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->barcode ?? 'N/A',
            $product->name,
            $product->category?->name ?? 'Sin categoría',
            number_format($product->cost_price, 2, '.', ''),
            number_format($product->sale_price, 2, '.', ''),
            $product->current_stock,
            $product->min_stock ?? 0,
            $product->is_active ? 'Activo' : 'Inactivo',
            $product->lot_number ?? 'N/A',
            $product->expiration_date ? $product->expiration_date->format('Y-m-d') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0F766E'] // Teal corporativo VetPets
                ],
            ],
        ];
    }
}