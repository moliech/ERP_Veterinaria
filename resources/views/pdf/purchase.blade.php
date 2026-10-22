@extends('pdf.layout')

@section('title', 'Orden de Compra ' . $purchase->purchase_number)

@section('doc_type', 'Orden de Compra')

@section('doc_number', $purchase->purchase_number)

@section('content')
<style>
    .section-title {
        font-size: 11px;
        font-weight: bold;
        color: #065f46;
        border-bottom: 1px solid #d1d5db;
        padding-bottom: 4px;
        margin-top: 10px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
    }

    .info-table td {
        padding: 3px 6px;
        vertical-align: top;
        font-size: 10px;
    }

    .label {
        font-weight: bold;
        color: #475569;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .status-badge {
        font-weight: bold;
        text-transform: uppercase;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 9px;
    }

    .status-pendiente { background-color: #fef3c7; color: #92400e; }
    .status-parcial { background-color: #dbeafe; color: #1e40af; }
    .status-recibida { background-color: #dcfce7; color: #166534; }
    .status-anulada { background-color: #fee2e2; color: #991b1b; }

    .notes {
        margin-top: 12px;
        padding: 8px;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        border-radius: 4px;
    }
</style>

<div class="section-title">DATOS DE LA ORDEN DE COMPRA</div>

<table class="info-table">
    <tr>
        <td style="width: 50%;">
            <span class="label">N° de Orden:</span>
            {{ $purchase->purchase_number }}
        </td>
        <td style="width: 50%;">
            <span class="label">Fecha de Emisión:</span>
            {{ $purchase->purchase_date?->format('d/m/Y') ?? 'No registrada' }}
        </td>
    </tr>
    <tr>
        <td>
            <span class="label">Estado Actual:</span>
            <span class="status-badge status-{{ $purchase->status }}">{{ ucfirst($purchase->status) }}</span>
        </td>
        <td>
            <span class="label">Solicitante:</span>
            {{ $purchase->user?->name ?? 'No asignado' }}
        </td>
    </tr>
    @if($purchase->received_at)
        <tr>
            <td colspan="2">
                <span class="label">Fecha de Recepción Completa:</span>
                {{ $purchase->received_at->format('d/m/Y H:i') }}
            </td>
        </tr>
    @endif
</table>

<div class="section-title">DATOS DEL PROVEEDOR / LABORATORIO</div>

<table class="info-table">
    <tr>
        <td style="width: 50%;">
            <span class="label">Razón Social:</span>
            {{ $purchase->provider?->company_name ?? 'Proveedor no disponible' }}
        </td>
        <td style="width: 50%;">
            <span class="label">NIT:</span>
            {{ $purchase->provider?->nit ?? 'No registrado' }}
        </td>
    </tr>
    <tr>
        <td>
            <span class="label">Persona de Contacto:</span>
            {{ $purchase->provider?->contact_person ?? 'Sin contacto' }}
        </td>
        <td>
            <span class="label">Teléfono:</span>
            {{ $purchase->provider?->phone ?? 'No registrado' }}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <span class="label">Dirección:</span>
            {{ $purchase->provider?->address ?? 'No registrada' }}
            @if($purchase->provider?->email)
                · <span class="label">Correo:</span> {{ $purchase->provider->email }}
            @endif
        </td>
    </tr>
</table>

<div class="section-title">DETALLE DE MEDICAMENTOS E INSUMOS SOLICITADOS</div>

<table class="data">
    <thead>
        <tr>
            <th style="width: 8%;" class="text-center">Ped.</th>
            <th style="width: 8%;" class="text-center">Rec.</th>
            <th style="width: 44%;">Producto / Medicamento</th>
            <th style="width: 20%;" class="text-right">Costo Unitario</th>
            <th style="width: 20%;" class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($purchase->details as $detail)
            <tr>
                <td class="text-center font-bold">{{ $detail->quantity }}</td>
                <td class="text-center font-bold" style="color: {{ $detail->received_quantity >= $detail->quantity ? '#166534' : ($detail->received_quantity > 0 ? '#1e40af' : '#64748b') }};">
                    {{ $detail->received_quantity }}
                </td>
                <td>{{ $detail->product?->name ?? 'Producto no disponible' }}</td>
                <td class="text-right">
                    ${{ number_format((float) $detail->unit_cost, 2, ',', '.') }}
                </td>
                <td class="text-right">
                    ${{ number_format((float) $detail->subtotal, 2, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No hay ítems registrados en esta orden.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<table class="totals">
    <tr>
        <td>Subtotal:</td>
        <td class="text-right">
            ${{ number_format((float) $purchase->subtotal, 2, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td>IVA (19%):</td>
        <td class="text-right">
            ${{ number_format((float) $purchase->tax, 2, ',', '.') }}
        </td>
    </tr>
    <tr class="total-row">
        <td>TOTAL:</td>
        <td class="text-right">
            ${{ number_format((float) $purchase->total, 2, ',', '.') }}
        </td>
    </tr>
</table>

@if ($purchase->notes)
    <div class="notes">
        <strong>Notas / Observaciones de Abastecimiento:</strong>
        <p style="margin: 4px 0 0 0;">{{ $purchase->notes }}</p>
    </div>
@endif

<p style="margin-top: 25px; text-align: center; font-size: 9px; color: #64748b;">
    Orden de compra generada automáticamente por VetPets ERP · Proceso de Abastecimiento Formal.
</p>
@endsection
