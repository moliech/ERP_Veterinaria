<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
                    <span>Orden de Compra: {{ $purchase->purchase_number }}</span>
                    @php
                        $badges = [
                            'pendiente' => 'bg-amber-100 text-amber-800 border-amber-300',
                            'parcial'   => 'bg-blue-100 text-blue-800 border-blue-300',
                            'recibida'  => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'anulada'   => 'bg-rose-100 text-rose-800 border-rose-300',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badges[$purchase->status] ?? 'bg-slate-100 text-slate-700' }}">
                        {{ ucfirst($purchase->status) }}
                    </span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Registrada el {{ $purchase->purchase_date?->format('d/m/Y') }} por {{ $purchase->user?->name ?? 'Usuario' }}</p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('purchases.index') }}" class="text-slate-600 hover:text-slate-900 font-medium text-xs flex items-center space-x-1 mr-2">
                    <i class="fa-solid fa-arrow-left"></i> <span>Volver a Compras</span>
                </a>

                <a href="{{ route('purchases.pdf', $purchase) }}" target="_blank"
                   class="bg-rose-600 hover:bg-rose-700 text-white font-medium py-2 px-3.5 rounded-xl text-xs transition flex items-center space-x-1.5 shadow-sm">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Descargar Orden PDF</span>
                </a>

                @can('anular-compras')
                    @if($purchase->status === 'pendiente' && !$purchase->trashed())
                        <form action="{{ route('purchases.destroy', $purchase) }}" method="POST"
                              onsubmit="return confirm('¿Seguro que deseas anular esta orden de compra? Solo es permitido si no tiene recepciones asociadas.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-medium py-2 px-3.5 rounded-xl text-xs transition flex items-center space-x-1.5 shadow-sm">
                                <i class="fa-solid fa-ban"></i>
                                <span>Anular Orden</span>
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded-xl shadow-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-800 rounded-xl shadow-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation text-red-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Datos Generales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información Proveedor -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-building mr-1"></i> Datos del Proveedor / Laboratorio
                    </span>
                    <div class="text-base font-bold text-slate-900">
                        {{ $purchase->provider?->company_name ?? 'Proveedor no disponible' }}
                    </div>
                    <div class="text-xs text-slate-600 space-y-1">
                        <div><strong class="text-slate-700">NIT:</strong> {{ $purchase->provider?->nit ?? 'N/D' }}</div>
                        <div><strong class="text-slate-700">Contacto:</strong> {{ $purchase->provider?->contact_person ?? 'Sin contacto' }}</div>
                        <div><strong class="text-slate-700">Teléfono:</strong> {{ $purchase->provider?->phone ?? 'N/D' }}</div>
                        <div><strong class="text-slate-700">Dirección:</strong> {{ $purchase->provider?->address ?? 'N/D' }}</div>
                        @if($purchase->provider?->email)
                            <div><strong class="text-slate-700">Correo:</strong> {{ $purchase->provider->email }}</div>
                        @endif
                    </div>
                </div>

                <!-- Resumen de Estado -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-clipboard-check mr-1"></i> Resumen Operativo de la Orden
                    </span>
                    <div class="grid grid-cols-2 gap-4 text-xs text-slate-600">
                        <div>
                            <span class="text-slate-400 block">Número Consecutivo:</span>
                            <span class="font-mono font-bold text-slate-900 text-sm">{{ $purchase->purchase_number }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Fecha de Registro:</span>
                            <span class="font-semibold text-slate-800">{{ $purchase->purchase_date?->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Comprador Responsable:</span>
                            <span class="font-semibold text-slate-800">{{ $purchase->user?->name ?? 'Usuario' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Recepción Total:</span>
                            <span class="font-semibold text-slate-800">
                                {{ $purchase->received_at ? $purchase->received_at->format('d/m/Y H:i') : 'Pendiente' }}
                            </span>
                        </div>
                    </div>
                    @if($purchase->notes)
                        <div class="pt-2 border-t border-slate-100 text-xs">
                            <span class="text-slate-400 font-semibold block">Observaciones:</span>
                            <p class="text-slate-700 italic">{{ $purchase->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tabla de Ítems: Pedido vs Recibido vs Pendiente -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        Control de Abastecimiento: Cantidad Pedida vs Recibida
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">Producto / Medicamento</th>
                                <th class="px-4 py-3 text-center">Cant. Pedida</th>
                                <th class="px-4 py-3 text-center">Cant. Recibida</th>
                                <th class="px-4 py-3 text-center">Pendiente</th>
                                <th class="px-4 py-3 text-right">Costo Unitario</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($purchase->details as $detail)
                                @php
                                    $pending = max(0, $detail->quantity - $detail->received_quantity);
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-900">
                                        {{ $detail->product?->name ?? 'Producto no disponible' }}
                                        <div class="text-[11px] text-slate-400 font-normal">
                                            Stock actual en bodega: {{ $detail->product?->current_stock ?? 0 }} unid.
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-800">
                                        {{ $detail->quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold {{ $detail->received_quantity >= $detail->quantity ? 'text-emerald-600' : ($detail->received_quantity > 0 ? 'text-blue-600' : 'text-slate-400') }}">
                                        {{ $detail->received_quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold {{ $pending > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                        {{ $pending }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-slate-700">
                                        ${{ number_format((float) $detail->unit_cost, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                        ${{ number_format((float) $detail->subtotal, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totales -->
                <div class="flex justify-end pt-2">
                    <div class="w-full sm:w-72 bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Neto:</span>
                            <span class="font-mono font-semibold">${{ number_format((float) $purchase->subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>IVA (19%):</span>
                            <span class="font-mono font-semibold">${{ number_format((float) $purchase->tax, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-900 font-bold text-base border-t border-slate-300 pt-2">
                            <span>TOTAL:</span>
                            <span class="font-mono text-emerald-600 text-lg">${{ number_format((float) $purchase->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de Recepción Física de Mercancía (Total o Parcial) -->
            @can('recibir-compras')
                @if(in_array($purchase->status, ['pendiente', 'parcial']) && !$purchase->trashed())
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-200 shadow-sm space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center text-lg">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                                    Recepción Física de Mercancía en Bodega
                                </h3>
                                <p class="text-xs text-slate-600">
                                    Ingrese las cantidades que llegaron físicamente en este lote. El stock se sumará de manera automática en el inventario.
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('purchases.receive', $purchase) }}" class="space-y-4">
                            @csrf

                            <div class="overflow-x-auto rounded-xl bg-white border border-blue-200">
                                <table class="min-w-full divide-y divide-blue-100 text-sm">
                                    <thead class="bg-blue-100/60 text-blue-900 uppercase text-[11px] font-bold">
                                        <tr>
                                            <th class="px-4 py-2.5 text-left">Producto</th>
                                            <th class="px-4 py-2.5 text-center">Pedido</th>
                                            <th class="px-4 py-2.5 text-center">Ya Recibido</th>
                                            <th class="px-4 py-2.5 text-center">Pendiente</th>
                                            <th class="px-4 py-2.5 text-center w-40">Recibir en este Lote</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-blue-50">
                                        @foreach($purchase->details as $detail)
                                            @php $pend = max(0, $detail->quantity - $detail->received_quantity); @endphp
                                            <tr>
                                                <td class="px-4 py-2.5 font-medium text-slate-900">
                                                    {{ $detail->product?->name }}
                                                </td>
                                                <td class="px-4 py-2.5 text-center font-bold text-slate-700">
                                                    {{ $detail->quantity }}
                                                </td>
                                                <td class="px-4 py-2.5 text-center text-emerald-700 font-bold">
                                                    {{ $detail->received_quantity }}
                                                </td>
                                                <td class="px-4 py-2.5 text-center text-amber-700 font-bold">
                                                    {{ $pend }}
                                                </td>
                                                <td class="px-4 py-2.5 text-center">
                                                    @if($pend > 0)
                                                        <input type="number" name="received[{{ $detail->id }}]"
                                                               value="{{ $pend }}" min="0" max="{{ $pend }}"
                                                               class="w-28 text-center border-blue-300 rounded-xl text-xs font-bold focus:ring-blue-500 focus:border-blue-500">
                                                    @else
                                                        <span class="text-xs text-emerald-600 font-bold">
                                                            <i class="fa-solid fa-circle-check"></i> Completo
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition flex items-center space-x-2 shadow-sm">
                                    <i class="fa-solid fa-dolly"></i>
                                    <span>Confirmar Recepción e Incrementar Stock</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @endcan

        </div>
    </div>
</x-app-layout>
