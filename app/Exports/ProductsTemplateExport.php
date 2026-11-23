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
                '7709991000011',
                'Snack Dental Pedigree Dentastix x7',
                'Snacks y Premios',
                '14500',
                '22000',
                '15',
                '5',
                'LOT-2026-SN01',
                '2027-10-30',
            ],
            [
                '7709991000028',
                'Collar Reflectivo Ajustable Talla M',
                'Accesorios y Paseo',
                '18000',
                '32000',
                '10',
                '3',
                '',
                '',
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

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E293B'],
                ],
            ],
        ];
    }
}
