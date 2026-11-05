<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kardex de Inventario (Libro Mayor)</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6 space-y-4">
                <form method="GET" action="{{ route('inventory.kardex') }}" class="flex flex-wrap items-end gap-2">
                    <div>
                        <x-input-label for="product" value="Producto" />
                        <select id="product" name="product" class="mt-1 border-gray-300 rounded-md" required>
                            <option value="">Seleccione un producto</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" @selected($product?->id === $p->id)>
                                    {{ $p->name }} (stock: {{ $p->stock }}){{ $p->trashed() ? ' — en papelera' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="concept" value="Concepto" />
                        <select id="concept" name="concept" class="mt-1 border-gray-300 rounded-md">
                            <option value="">Todos</option>
                            @foreach ($concepts as $value => $label)
                                <option value="{{ $value }}" @selected(request('concept') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="from" value="Desde" />
                        <input id="from" type="date" name="from" value="{{ request('from') }}" class="mt-1 border-gray-300 rounded-md">
                    </div>
                    <div>
                        <x-input-label for="to" value="Hasta" />
                        <input id="to" type="date" name="to" value="{{ request('to') }}" class="mt-1 border-gray-300 rounded-md">
                    </div>
                    <x-primary-button>Consultar</x-primary-button>
                </form>

                @if ($product)
                    <div class="flex flex-wrap items-center justify-between gap-4 border-t pt-4">
                        <div>
                            <p class="text-lg font-semibold">{{ $product->name }}</p>
                            <p class="text-sm text-gray-600">
                                Stock en el sistema: <strong>{{ $product->stock }}</strong> ·
                                Entradas: <strong class="text-green-700">{{ $totals['entradas'] }}</strong> ·
                                Salidas: <strong class="text-red-700">{{ $totals['salidas'] }}</strong>
                            </p>
                            @if ($totals['cuadra'])
                                <p class="text-sm text-green-700 font-semibold">✓ El saldo del Kardex coincide exactamente con el stock en bodega.</p>
                            @else
                                <p class="text-sm text-red-700 font-semibold">⚠ El saldo del Kardex NO coincide con el stock.</p>
                            @endif
                        </div>
                        <div class="space-x-3">
                            <a href="{{ route('inventory.pdf', ['product' => $product->id] + request()->only('from', 'to', 'concept')) }}" target="_blank" class="text-blue-700 underline font-semibold">Descargar PDF</a>
                            @can('ajustar-inventario')
                                @unless ($product->trashed())
                                    <a href="{{ route('inventory.adjust', $product) }}" class="text-amber-700 underline font-semibold">Ajustar inventario</a>
                                @endunless
                            @endcan
                        </div>
                    </div>

                    <table class="min-w-full text-sm mt-4">
                        <thead>
                            <tr class="border-b text-left bg-slate-50">
                                <th class="p-2">Fecha</th>
                                <th class="p-2">Concepto</th>
                                <th class="p-2">Documento</th>
                                <th class="p-2 text-right">Entrada</th>
                                <th class="p-2 text-right">Salida</th>
                                <th class="p-2 text-right font-bold">Saldo</th>
                                <th class="p-2 text-right">Valor Unit.</th>
                                <th class="p-2">Usuario</th>
                                <th class="p-2">Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movements as $m)
                                <tr class="border-b hover:bg-slate-50 transition">
                                    <td class="p-2">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="p-2">{{ $m->conceptLabel() }}</td>
                                    <td class="p-2 font-mono text-xs">{{ $m->referenceLabel() ?? '—' }}</td>
                                    <td class="p-2 text-right text-green-700 font-semibold">{{ $m->type === 'entrada' ? '+' . $m->quantity : '' }}</td>
                                    <td class="p-2 text-right text-red-700 font-semibold">{{ $m->type === 'salida' ? '-' . $m->quantity : '' }}</td>
                                    <td class="p-2 text-right font-bold bg-slate-50/50">{{ $m->balance }}</td>
                                    <td class="p-2 text-right">{{ $m->unit_cost !== null ? '$' . number_format((float) $m->unit_cost, 2) : '' }}</td>
                                    <td class="p-2">{{ $m->user?->name ?? 'Sistema' }}</td>
                                    <td class="p-2 text-xs text-gray-500">{{ $m->notes }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="p-4 text-center text-gray-500">No hay movimientos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $movements->links() }}
                @else
                    <p class="text-sm text-gray-500">Seleccione un producto para ver su libro mayor de inventario.</p>
                @endif
            </div>
        </div>
    </div>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajuste de inventario: {{ $product->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('inventory.adjust.store', $product) }}" class="bg-white shadow rounded p-6 space-y-4">
                @csrf
                <p class="text-sm text-gray-600">
                    Un ajuste se realiza tras un <strong>conteo físico</strong>: digite la cantidad real encontrada en bodega y el sistema registrará la diferencia como entrada o salida en el Kardex.
                </p>

                <div class="rounded bg-gray-50 p-3">
                    Stock actual en el sistema: <strong class="text-lg">{{ $product->stock }}</strong> unidades
                </div>

                <div>
                    <x-input-label for="counted_stock" value="Cantidad contada físicamente en bodega" />
                    <x-text-input id="counted_stock" name="counted_stock" type="number" min="0" step="1" class="mt-1 block w-48 font-bold text-lg" :value="old('counted_stock', $product->stock)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('counted_stock')" />
                </div>

                <div>
                    <x-input-label for="reason" value="Motivo del ajuste (obligatorio, mín. 10 caracteres)" />
                    <x-textarea id="reason" name="reason" rows="2" class="mt-1 block w-full" placeholder="Ej.: Conteo físico de cierre mensual: 1 unidad averiada por humedad" required>{{ old('reason') }}</x-textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <x-primary-button onclick="return confirm('¿Confirma registrar el ajuste físico de inventario?')">Registrar ajuste</x-primary-button>
                    <a href="{{ route('inventory.kardex', ['product' => $product->id]) }}" class="underline text-sm text-slate-600">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>