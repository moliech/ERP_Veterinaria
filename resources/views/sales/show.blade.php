<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center font-bold">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">
                        Factura de Venta: <span class="text-emerald-600">{{ $sale->invoice_number }}</span>
                    </h2>
                    <span class="text-xs text-slate-500">Comprobante de Transacción VetPets ERP</span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium py-2 px-4 rounded-xl text-xs transition flex items-center space-x-2 shadow-sm">
                    <i class="fa-solid fa-print"></i>
                    <span>Imprimir Comprobante</span>
                </button>
                <a href="{{ route('sales.index') }}" class="text-slate-600 hover:text-slate-900 font-medium text-xs flex items-center space-x-1">
                    <i class="fa-solid fa-arrow-left"></i> <span>Volver a Ventas</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto space-y-6">

            <!-- Card de Factura -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-6">

                <!-- Encabezado de Factura -->
                <div class="flex flex-col md:flex-row justify-between border-b border-slate-100 pb-6 gap-6">
                    <div>
                        <div class="flex items-center space-x-2 text-emerald-600 font-black text-xl tracking-wide mb-1">
                            <i class="fa-solid fa-paw"></i>
                            <span>VetPets ERP</span>
                        </div>
                        <p class="text-xs text-slate-500">Clínica Veterinaria & PetShop Especializado</p>
                        <p class="text-xs text-slate-500">NIT: 900.123.456-7 | Régimen Común</p>
                        <p class="text-xs text-slate-500">Cartago, Valle del Cauca, Colombia</p>
                    </div>

                    <div class="md:text-right space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Número de Comprobante</span>
                        <span class="text-xl font-mono font-black text-slate-900 block">{{ $sale->invoice_number }}</span>
                        <div class="text-xs text-slate-600">
                            <strong>Fecha:</strong> {{ $sale->sale_date->format('d/m/Y') }}
                        </div>
                        <div class="text-xs text-slate-600">
                            <strong>Atendido por:</strong> {{ $sale->user->name ?? 'Usuario del Sistema' }}
                        </div>
                        <div class="pt-1">
                            @if ($sale->status === 'pagada')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <i class="fa-solid fa-circle-check mr-1.5 text-xs"></i> Pagada
                                </span>
                            @elseif ($sale->status === 'anulada')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    <i class="fa-solid fa-circle-xmark mr-1.5 text-xs"></i> Anulada
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Datos del Cliente / Propietario -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Información del Cliente / Propietario</span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                        <div>
                            <span class="text-slate-500 block">Nombre Completo:</span>
                            <strong class="text-slate-800 text-sm">{{ $sale->client->name ?? 'Cliente Desconocido' }} {{ $sale->client->first_surname ?? '' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Documento de Identidad:</span>
                            <strong class="text-slate-800">{{ $sale->client->document_type ?? 'CC' }}: {{ $sale->client->document_number ?? 'N/A' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Contacto / Dirección:</span>
                            <span class="text-slate-700">{{ $sale->client->address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Productos y Servicios -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-900 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs uppercase font-bold">Descripción del Producto</th>
                                <th class="px-4 py-3 text-center text-xs uppercase font-bold w-24">Cantidad</th>
                                <th class="px-4 py-3 text-right text-xs uppercase font-bold w-36">Precio Unitario</th>
                                <th class="px-4 py-3 text-right text-xs uppercase font-bold w-36">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($sale->details as $detail)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 text-sm font-semibold text-slate-900">
                                        {{ $detail->product->name ?? 'Producto no disponible' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-700">
                                        {{ $detail->quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-slate-700">
                                        ${{ number_format($detail->unit_price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-slate-900">
                                        ${{ number_format($detail->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Resumen de Liquidación de Impuestos -->
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <div class="w-72 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Base:</span>
                            <span class="font-medium">${{ number_format($sale->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>IVA Generado (19%):</span>
                            <span class="font-medium">${{ number_format($sale->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-black text-emerald-700 pt-2 border-t border-slate-200">
                            <span>Total Factura:</span>
                            <span>${{ number_format($sale->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if ($sale->notes)
                    <div class="bg-slate-50 p-3 rounded-xl text-xs text-slate-600 border border-slate-100">
                        <strong>Observaciones:</strong> {{ $sale->notes }}
                    </div>
                @endif

                <!-- Acciones al pie -->
                <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                    <a href="{{ route('sales.index') }}" class="text-slate-600 hover:text-slate-900 text-xs font-semibold flex items-center space-x-1">
                        <i class="fa-solid fa-arrow-left"></i> <span>Regresar al Listado</span>
                    </a>

                    @if ($sale->status !== 'anulada')
                        @can('eliminar-ventas')
                            <form action="{{ route('sales.destroy', $sale) }}" method="POST"
                                  onsubmit="return confirm('¿Confirmas anular esta venta? El stock volverá al inventario.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-300 font-semibold px-4 py-2 rounded-xl text-xs transition flex items-center space-x-1.5">
                                    <i class="fa-solid fa-ban"></i>
                                    <span>Anular Factura y Devolver Stock</span>
                                </button>
                            </form>
                        @endcan
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
