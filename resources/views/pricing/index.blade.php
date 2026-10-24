<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Módulo de Tarifas, Precios de Venta e IVA Diferencial') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Fijación de precios comerciales sobre costo de bodega, margen de rentabilidad y liquidación de IVA por producto (0%, 5%, 19%)
                </p>
            </div>

            <a href="{{ route('products.index') }}"
               class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl transition flex items-center space-x-1.5">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Ir a Inventario y Lotes (Bodega)</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs de Tarifas e Impuestos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Referencias en Tarifario</span>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $summary['total_products'] }}</div>
                        <span class="text-[11px] text-emerald-600 font-semibold">Catálogo activo</span>
                    </div>
                    <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tarifa General (19%)</span>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $summary['general_19'] }}</div>
                        <span class="text-[11px] text-slate-500 font-semibold">Accesorios, estética y snacks</span>
                    </div>
                    <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">IVA Diferencial (5%)</span>
                        <div class="text-2xl font-extrabold text-amber-600 mt-1">{{ $summary['differential_5'] }}</div>
                        <span class="text-[11px] text-amber-600 font-semibold">Alimentos concentrados / agro</span>
                    </div>
                    <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Exentos / Excluidos (0%)</span>
                        <div class="text-2xl font-extrabold text-green-700 mt-1">{{ $summary['exempt_0'] }}</div>
                        <span class="text-[11px] text-green-600 font-semibold">Suplementos e insumos exentos</span>
                    </div>
                    <div class="w-11 h-11 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-5">

                <!-- Filtros -->
                <form method="GET" action="{{ route('pricing.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Buscar producto o código..."
                               class="w-full px-4 py-2 border border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <select name="category_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Todas las Categorías</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) $categoryId === (string) $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="tax_rate" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Todas las Tarifas de IVA</option>
                            <option value="19" @selected($taxFilter === '19')>19% — Tarifa General</option>
                            <option value="5" @selected($taxFilter === '5')>5% — Tarifa Diferencial</option>
                            <option value="0" @selected($taxFilter === '0')>0% — Exento / Excluido</option>
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                            Filtrar
                        </button>
                        @if ($search !== '' || $categoryId || ($taxFilter !== null && $taxFilter !== ''))
                            <a href="{{ route('pricing.index') }}" class="px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-600 hover:bg-slate-100 flex items-center justify-center">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Formulario masivo de Tarifas -->
                <form method="POST" action="{{ route('pricing.bulk-update') }}">
                    @csrf
                    @method('PUT')

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-900 text-white text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3 text-left">Producto / Categoría</th>
                                    <th class="px-4 py-3 text-right">Costo Bodega</th>
                                    <th class="px-4 py-3 text-right w-44">Precio Base (sin IVA)</th>
                                    <th class="px-4 py-3 text-center">Margen (%)</th>
                                    <th class="px-4 py-3 text-center w-52">Tarifa IVA Diferencial</th>
                                    <th class="px-4 py-3 text-right">PVP Final (con IVA)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($products as $product)
                                    <tr class="hover:bg-slate-50 transition"
                                        x-data="{
                                            cost: {{ (float) $product->cost_price }},
                                            sale: {{ (float) $product->sale_price }},
                                            tax: '{{ number_format((float) $product->tax_rate, 2, '.', '') }}',
                                            get margin() {
                                                if (this.cost <= 0) return this.sale > 0 ? 100 : 0;
                                                return (((this.sale - this.cost) / this.cost) * 100).toFixed(1);
                                            },
                                            get finalPrice() {
                                                const rate = parseFloat(this.tax || 0);
                                                const val = this.sale * (1 + (rate / 100));
                                                return '$' + Math.round(val).toLocaleString('es-CO');
                                            }
                                        }">
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-slate-900">{{ $product->name }}</div>
                                            <div class="text-xs text-slate-500 flex items-center space-x-2 mt-0.5">
                                                <span class="font-mono">{{ $product->barcode ?? 'S/C' }}</span>
                                                <span>·</span>
                                                <span class="text-indigo-600 font-semibold">{{ $product->category->name ?? 'Sin Categoría' }}</span>
                                                @if ($product->batch_code)
                                                    <span>·</span>
                                                    <span class="font-mono text-slate-600">Lote: {{ $product->batch_code }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 text-right font-mono text-slate-600">
                                            ${{ number_format((float) $product->cost_price, 0, ',', '.') }}
                                            <div class="text-[10px] text-slate-400">Desde Inventario</div>
                                        </td>

                                        <td class="px-4 py-3 text-right">
                                            <input type="number" step="0.01" min="0"
                                                   name="rates[{{ $product->id }}][sale_price]"
                                                   x-model.number="sale"
                                                   class="w-36 text-right font-mono font-bold border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                                   @cannot('editar-productos') disabled @endcannot>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                                  :class="margin < 15 ? 'bg-red-100 text-red-800' : (margin < 30 ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800')">
                                                <span x-text="margin + '%'">{{ $product->profitMargin() }}%</span>
                                            </span>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <select name="rates[{{ $product->id }}][tax_rate]"
                                                    x-model="tax"
                                                    class="w-48 border-slate-300 rounded-xl text-xs font-semibold focus:ring-emerald-500 focus:border-emerald-500"
                                                    @cannot('editar-productos') disabled @endcannot>
                                                <option value="19.00">19% — General</option>
                                                <option value="5.00">5% — Diferencial</option>
                                                <option value="0.00">0% — Exento / Excluido</option>
                                            </select>
                                        </td>

                                        <td class="px-4 py-3 text-right font-mono font-extrabold text-emerald-700 text-base">
                                            <span x-text="finalPrice">${{ number_format($product->priceWithTax(), 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                            No hay productos que coincidan con el filtro seleccionado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-slate-100">
                        <div>
                            {{ $products->links() }}
                        </div>

                        @can('editar-productos')
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm text-sm transition flex items-center space-x-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Guardar Tarifas e IVA de esta Página</span>
                            </button>
                        @endcan
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
