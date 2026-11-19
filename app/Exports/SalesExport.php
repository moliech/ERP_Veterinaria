<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Sale::with(['client', 'user'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Número de Venta',
            'Fecha y Hora',
            'Cliente',
            'Vendedor / Usuario',
            'Subtotal',
            'IVA (19%)',
            'Total Facturado',
            'Método de Pago',
            'Estado',
        ];
    }

    /**
     * @param Sale $sale
     */
    public function map($sale): array
    {
        return [
            $sale->sale_number,
            $sale->created_at->format('Y-m-d H:i:s'),
            $sale->client?->name ?? 'Cliente General / Mostrador',
            $sale->user?->name ?? 'Sistema',
            number_format($sale->subtotal, 2, '.', ''),
            number_format($sale->tax, 2, '.', ''),
            number_format($sale->total, 2, '.', ''),
            ucfirst($sale->payment_method ?? 'efectivo'),
            ucfirst($sale->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0369A1'] // Sky 700
                ],
            ],
        ];
    }
}