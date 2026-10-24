<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Inventario de Bodega, Costos y Control de Lotes') }} {{ isset($showTrashed) && $showTrashed ? '(Papelera)' : '' }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Control de existencias físicas, costo unitario de adquisición, número de lote y fechas de vencimiento
                </p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('pricing.index') }}"
                   class="bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm text-xs transition flex items-center space-x-2">
                    <i class="fa-solid fa-tags"></i>
                    <span>Módulo de Tarifas e IVA</span>
                </a>

                @can('crear-productos')
                    <a href="{{ route('products.create') }}"
                       class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm text-sm transition flex items-center space-x-2">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Registrar Producto / Lote</span>
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">

                <div class="flex flex-wrap justify-between items-center pb-2 border-b border-slate-100 gap-2">
                    <span class="text-xs uppercase font-bold tracking-wider text-slate-500">
                        Existencias en Bodega · Costo de Adquisición · Trazabilidad de Lotes y Vencimientos
                    </span>

                    @can('eliminar-productos')
                        @if (isset($showTrashed) && $showTrashed)
                            <a href="{{ route('products.index') }}" class="text-sm text-emerald-600 font-bold hover:underline flex items-center space-x-1">
                                <i class="fa-solid fa-boxes-stacked text-xs"></i>
                                <span>Ver Inventario Activo</span>
                            </a>
                        @else
                            <a href="{{ route('products.index', ['trashed' => 1]) }}" class="text-sm text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                                <span>Ver Papelera</span>
                            </a>
                        @endif
                    @endcan
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-900 text-white text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Código</th>
                                <th class="px-4 py-3 text-left">Producto / Categoría</th>
                                <th class="px-4 py-3 text-left">Lote y Vencimiento</th>
                                <th class="px-4 py-3 text-right">Costo Bodega</th>
                                <th class="px-4 py-3 text-center">Stock Físico</th>
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse ($products as $product)
                                @php
                                    $exp = $product->expirationStatus();
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-mono text-xs text-slate-700 whitespace-nowrap">
                                        {{ $product->barcode ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900">{{ $product->name }}</div>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 text-[11px] font-semibold rounded-full bg-blue-50 text-blue-700">
                                            {{ $product->category->name ?? 'Sin Categoría' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col space-y-1">
                                            <div class="text-xs font-mono font-semibold text-slate-800">
                                                Lote: {{ $product->batch_code ?? 'SIN-LOTE' }}
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $exp['badge'] }}">
                                                    {{ $exp['label'] }}
                                                </span>
                                                @if ($product->expiration_date)
                                                    <span class="text-xs text-slate-500">
                                                        Vence: {{ $product->expiration_date->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-slate-800 whitespace-nowrap">
                                        ${{ number_format((float) $product->cost_price, 0, ',', '.') }}
                                        <div class="text-[10px] font-sans font-normal text-slate-400">
                                            PVP en Tarifas: ${{ number_format((float) $product->sale_price, 0, ',', '.') }} (+IVA {{ number_format((float) $product->tax_rate, 0) }}%)
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $product->current_stock <= $product->minimum_stock ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-800' }}">
                                            {{ $product->current_stock }} uds
                                        </span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Mín: {{ $product->minimum_stock }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium space-x-2 whitespace-nowrap">
                                        @if ($product->trashed())
                                            @can('eliminar-productos')
                                                <form action="{{ route('products.restore', $product) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-600 hover:text-green-900 font-bold">
                                                        Restaurar
                                                    </button>
                                                </form>
                                            @endcan
                                        @else
                                            @can('editar-productos')
                                                <a href="{{ route('products.edit', $product) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 font-bold">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('eliminar-productos')
                                                <form action="{{ route('products.destroy', $product) }}"
                                                      method="POST" class="inline-block"
                                                      onsubmit="return confirm('¿Enviar producto a la papelera?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                        No existen productos registrados en el inventario.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
