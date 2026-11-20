<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                '7701234567890',
                'Royal Canin Maxi Adult 15kg',
                'Alimento Perros',
                '180000',
                '245000',
                '10',
                '3',
                'L-2026-01',
                '2027-12-31',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'codigo_barras',
            'nombre',
            'categoria',
            'precio_costo',
            'precio_venta',
            'stock_inicial',
            'stock_minimo',
            'lote',
            'fecha_vencimiento',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E293B'], // Slate 800
                ],
            ],
        ];
    }
}
