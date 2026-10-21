@extends('pdf.layout')

@section('title', 'Factura de venta ' . $sale->invoice_number)

@section('doc_type', 'Factura de venta POS')

@section('doc_number', $sale->invoice_number)

@section('content')
<style>
.section-title {
font-size: 12px;
font-weight: bold;
color: #065f46;
border-bottom: 1px solid #d1d5db;
padding-bottom: 5px;
margin-top: 12px;
}

    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }

    .info-table td {
        padding: 4px 6px;
        vertical-align: top;
    }

    .label {
        font-weight: bold;
        color: #475569;
    }

    .text-right {
        text-align: right;
    }

    .status {
        font-weight: bold;
        text-transform: uppercase;
    }

    .notes {
        margin-top: 15px;
        padding: 8px;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="section-title">INFORMACIÓN DE LA VENTA</div>

<table class="info-table">
    <tr>
        <td style="width: 50%;">
            <span class="label">Factura:</span>
            {{ $sale->invoice_number }}
        </td>
        <td style="width: 50%;">
            <span class="label">Fecha:</span>
            {{ $sale->sale_date?->format('d/m/Y') ?? 'No registrada' }}
        </td>
    </tr>
    <tr>
        <td>
            <span class="label">Estado:</span>
            <span class="status">{{ $sale->status ?? 'Registrada' }}</span>
        </td>
        <td>
            <span class="label">Vendedor:</span>
            {{ $sale->user?->name ?? 'No asignado' }}
        </td>
    </tr>
</table>

<div class="section-title">DATOS DEL CLIENTE</div>

<table class="info-table">
    <tr>
        <td style="width: 50%;">
            <span class="label">Nombre:</span>
            {{ trim(($sale->client?->first_name ?? '') . ' ' . ($sale->client?->last_name ?? '')) ?: 'Cliente no disponible' }}
        </td>
        <td style="width: 50%;">
            <span class="label">Documento:</span>
            {{ $sale->client?->document_number ?? 'No registrado' }}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <span class="label">Dirección:</span>
            {{ $sale->client?->address ?? 'No registrada' }}
        </td>
    </tr>
</table>

<div class="section-title">DETALLE DE PRODUCTOS</div>

<table class="data">
    <thead>
        <tr>
            <th style="width: 9%;">Cant.</th>
            <th style="width: 37%;">Producto</th>
            <th style="width: 22%;" class="text-right">Precio unitario</th>
            <th style="width: 32%;" class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sale->details as $detail)
            <tr>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->product?->name ?? 'Producto no disponible' }}</td>
                <td class="text-right">
                    ${{ number_format((float) $detail->unit_price, 2, ',', '.') }}
                </td>
                <td class="text-right">
                    ${{ number_format((float) $detail->subtotal, 2, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">No hay productos registrados en esta venta.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<table class="totals">
    <tr>
        <td>Subtotal:</td>
        <td class="text-right">
            ${{ number_format((float) $sale->subtotal, 2, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td>Impuestos:</td>
        <td class="text-right">
            ${{ number_format((float) $sale->tax, 2, ',', '.') }}
        </td>
    </tr>
    <tr class="total-row">
        <td>TOTAL:</td>
        <td class="text-right">
            ${{ number_format((float) $sale->total, 2, ',', '.') }}
        </td>
    </tr>
</table>

@if ($sale->notes)
    <div class="notes">
        <strong>Observaciones:</strong>
        {{ $sale->notes }}
    </div>
@endif

<p style="margin-top: 25px; text-align: center; font-size: 9px; color: #64748b;">
    Gracias por confiar en VetPets ERP.
</p>