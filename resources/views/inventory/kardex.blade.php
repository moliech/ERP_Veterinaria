<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kardex de Inventario (Libro Mayor)</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl p-6 space-y-5">
                <form method="GET" action="{{ route('inventory.kardex') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="product" value="Producto" />
                        <select id="product" name="product" class="mt-1 border-slate-300 rounded-xl text-sm">
                            <option value="">Todos los productos</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" @selected($product?->id === $p->id)>
                                    {{ $p->name }} (stock: {{ $p->stock }}){{ $p->trashed() ? ' — en papelera' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="concept" value="Concepto" />
                        <select id="concept" name="concept" class="mt-1 border-slate-300 rounded-xl text-sm">
                            <option value="">Todos</option>
                            @foreach ($concepts as $value => $label)
                                <option value="{{ $value }}" @selected(request('concept') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="from" value="Desde" />
                        <input id="from" type="date" name="from" value="{{ request('from') }}" class="mt-1 border-slate-300 rounded-xl text-sm">
                    </div>
                    <div>
                        <x-input-label for="to" value="Hasta" />
                        <input id="to" type="date" name="to" value="{{ request('to') }}" class="mt-1 border-slate-300 rounded-xl text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <x-primary-button>Consultar</x-primary-button>
                        @if (request()->anyFilled(['product', 'concept', 'from', 'to']))
                            <a href="{{ route('inventory.kardex') }}" class="px-3.5 py-2 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-xs font-semibold transition">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>

                <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-4">
                    @if ($product)
                        <div>
                            <p class="text-lg font-bold text-slate-900">{{ $product->name }}</p>
                            <p class="text-sm text-slate-600">
                                Stock en el sistema: <strong>{{ $product->stock }}</strong> ·
                                Entradas: <strong class="text-emerald-700">{{ $totals['entradas'] }}</strong> ·
                                Salidas: <strong class="text-rose-700">{{ $totals['salidas'] }}</strong>
                            </p>
                            @if ($totals['cuadra'])
                                <p class="text-sm text-emerald-700 font-semibold">✓ El saldo del Kardex coincide exactamente con el stock en bodega.</p>
                            @else
                                <p class="text-sm text-rose-700 font-semibold">⚠ El saldo del Kardex NO coincide con el stock.</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('inventory.pdf', ['product' => $product->id] + request()->only('from', 'to', 'concept')) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 text-xs font-bold transition">
                                <i class="fa-solid fa-file-pdf mr-1"></i> Descargar PDF
                            </a>
                            @can('ajustar-inventario')
                                @unless ($product->trashed())
                                    <a href="{{ route('inventory.adjust', $product) }}" class="px-3.5 py-2 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 text-xs font-bold transition">
                                        <i class="fa-solid fa-sliders mr-1"></i> Ajustar inventario
                                    </a>
                                @endunless
                            @endcan
                        </div>
                    @else
                        <div>
                            <p class="text-lg font-bold text-slate-900">Movimientos Generales — Todos los Productos</p>
                            <p class="text-sm text-slate-600">
                                Entradas en el período: <strong class="text-emerald-700">+{{ $totals['entradas'] }}</strong> ·
                                Salidas en el período: <strong class="text-rose-700">-{{ $totals['salidas'] }}</strong>
                            </p>
                        </div>
                        <span class="text-xs text-slate-500">Seleccione un producto específico para exportar su PDF o registrar un ajuste físico.</span>
                    @endif
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                            <tr class="text-left">
                                <th class="px-3 py-3">Fecha</th>
                                @unless ($product)
                                    <th class="px-3 py-3">Producto</th>
                                @endunless
                                <th class="px-3 py-3">Concepto</th>
                                <th class="px-3 py-3">Documento</th>
                                <th class="px-3 py-3 text-right">Entrada</th>
                                <th class="px-3 py-3 text-right">Salida</th>
                                <th class="px-3 py-3 text-right font-bold">Saldo</th>
                                <th class="px-3 py-3 text-right">Valor Unit.</th>
                                <th class="px-3 py-3">Usuario</th>
                                <th class="px-3 py-3">Notas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($movements as $m)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-3 py-2.5 whitespace-nowrap font-mono text-xs text-slate-600">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                    @unless ($product)
                                        <td class="px-3 py-2.5 font-semibold text-slate-800">
                                            @if ($m->product)
                                                <a href="{{ route('inventory.kardex', ['product' => $m->product_id] + request()->only('from', 'to', 'concept')) }}" class="text-blue-700 hover:underline">
                                                    {{ $m->product->name }}
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    @endunless
                                    <td class="px-3 py-2.5">{{ $m->conceptLabel() }}</td>
                                    <td class="px-3 py-2.5 font-mono text-xs">{{ $m->referenceLabel() ?? '—' }}</td>
                                    <td class="px-3 py-2.5 text-right text-emerald-700 font-semibold">{{ $m->type === 'entrada' ? '+' . $m->quantity : '' }}</td>
                                    <td class="px-3 py-2.5 text-right text-rose-700 font-semibold">{{ $m->type === 'salida' ? '-' . $m->quantity : '' }}</td>
                                    <td class="px-3 py-2.5 text-right font-bold bg-slate-50/50">{{ $m->balance }}</td>
                                    <td class="px-3 py-2.5 text-right font-mono text-xs">{{ $m->unit_cost !== null ? '$' . number_format((float) $m->unit_cost, 0, ',', '.') : '—' }}</td>
                                    <td class="px-3 py-2.5">{{ $m->user?->name ?? 'Sistema' }}</td>
                                    <td class="px-3 py-2.5 text-xs text-slate-500">{{ $m->notes }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $product ? 9 : 10 }}" class="px-4 py-6 text-center text-slate-500">No hay movimientos registrados para los filtros seleccionados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
