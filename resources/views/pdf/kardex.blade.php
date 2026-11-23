@extends('pdf.layout')

@section('title', 'Kardex ' . $product->name)
@section('doc_type', 'Kardex de Inventario')
@section('doc_number', 'PROD-' . str_pad((string) $product->id, 5, '0', STR_PAD_LEFT))

@section('content')
    @php
        $fechaInicio = $from
            ? \Carbon\Carbon::parse($from)->format('d/m/Y')
            : ($movements->min('created_at')?->format('d/m/Y') ?? now()->format('d/m/Y'));
        $fechaFin = $to
            ? \Carbon\Carbon::parse($to)->format('d/m/Y')
            : now()->format('d/m/Y');
    @endphp

    <table class="info">
        <tr>
            <td style="width: 58%;">
                <strong>Producto:</strong> {{ $product->name }}<br>
                <strong>Stock actual:</strong> {{ $product->stock }} unidades
            </td>
            <td style="width: 42%;">
                <strong>Período:</strong> {{ $fechaInicio }} al {{ $fechaFin }}<br>
                <strong>Entradas:</strong> {{ $totals['entradas'] }} · <strong>Salidas:</strong> {{ $totals['salidas'] }}
            </td>
        </tr>
    </table>

    <table class="items" style="width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 8.5px;">
        <thead>
            <tr>
                <th style="width: 12%; padding: 5px 4px; text-align: left;">Fecha</th>
                <th style="width: 15%; padding: 5px 4px; text-align: left;">Concepto</th>
                <th style="width: 11%; padding: 5px 4px; text-align: left;">Documento</th>
                <th style="width: 7%; padding: 5px 4px; text-align: right;">Entr.</th>
                <th style="width: 7%; padding: 5px 4px; text-align: right;">Sal.</th>
                <th style="width: 7%; padding: 5px 4px; text-align: right;">Saldo</th>
                <th style="width: 11%; padding: 5px 4px; text-align: right;">Valor Unit.</th>
                <th style="width: 10%; padding: 5px 4px; text-align: left;">Usuario</th>
                <th style="width: 20%; padding: 5px 4px; text-align: left;">Notas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movements as $m)
                <tr>
                    <td style="padding: 5px 4px; word-wrap: break-word;">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 5px 4px; word-wrap: break-word;">{{ $m->conceptLabel() }}</td>
                    <td style="padding: 5px 4px; word-wrap: break-word;">{{ $m->referenceLabel() ?? '—' }}</td>
                    <td style="padding: 5px 4px; text-align: right; color: #047857;">{{ $m->type === 'entrada' ? '+' . $m->quantity : '' }}</td>
                    <td style="padding: 5px 4px; text-align: right; color: #be123c;">{{ $m->type === 'salida' ? '-' . $m->quantity : '' }}</td>
                    <td style="padding: 5px 4px; text-align: right;"><strong>{{ $m->balance }}</strong></td>
                    <td style="padding: 5px 4px; text-align: right;">{{ $m->unit_cost !== null ? '$' . number_format((float) $m->unit_cost, 0, ',', '.') : '—' }}</td>
                    <td style="padding: 5px 4px; word-wrap: break-word;">{{ $m->user?->name ?? 'Sistema' }}</td>
                    <td style="padding: 5px 4px; word-wrap: break-word;">{{ $m->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
