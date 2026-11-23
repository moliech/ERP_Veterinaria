<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Enumerable
    {
        return Sale::withTrashed()->with(['client', 'user'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Número de Factura',
            'Fecha de Venta',
            'Cliente',
            'Vendedor / Usuario',
            'Subtotal',
            'IVA',
            'Total Facturado',
            'Estado',
        ];
    }

    /**
     * @param  Sale  $sale
     */
    public function map($sale): array
    {
        $clientName = $sale->client
            ? trim($sale->client->first_name.' '.$sale->client->last_name)
            : 'Cliente General / Mostrador';

        return [
            $sale->invoice_number,
            $sale->sale_date ? $sale->sale_date->format('Y-m-d') : $sale->created_at->format('Y-m-d'),
            $clientName,
            $sale->user?->name ?? 'Sistema',
            number_format((float) $sale->subtotal, 2, '.', ''),
            number_format((float) $sale->tax, 2, '.', ''),
            number_format((float) $sale->total, 2, '.', ''),
            ucfirst($sale->status),
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0369A1'],
                ],
            ],
        ];
    }
}
