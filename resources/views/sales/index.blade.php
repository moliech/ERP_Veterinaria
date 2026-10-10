<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Ventas y Facturación (POS)') }}
            </h2>

            @can('crear-ventas')
                <a href="{{ route('sales.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition flex items-center space-x-2">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>+ Registrar Nueva Venta</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Mensajes de Notificación -->
            @if (session('success'))
                <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-sm flex items-center space-x-3">
                    <i class="fa-solid fa-circle-check text-xl text-emerald-600"></i>
                    <div>
                        <p class="font-bold">¡Operación Exitosa!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-800 p-4 rounded-xl shadow-sm flex items-center space-x-3">
                    <i class="fa-solid fa-circle-exclamation text-xl text-red-600"></i>
                    <div>
                        <p class="font-bold">Atención</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Barra Superior: Total del Mes y Filtros -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5 mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                            <i class="fa-solid fa-sack-dollar text-2xl"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Facturación Acumulada del Mes</span>
                            <span class="text-2xl font-black text-slate-900">${{ number_format((float) $totalMonth, 2) }}</span>
                        </div>
                    </div>

                    <!-- Formulario de Búsqueda y Filtros de Fecha -->
                    <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap items-center gap-3">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Buscar factura o cliente..." 
                                   class="pl-9 pr-4 py-2 text-sm border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm w-56">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                        </div>

                        <div class="flex items-center space-x-2 text-xs text-slate-500">
                            <span>Desde:</span>
                            <input type="date" name="from" value="{{ request('from') }}" 
                                   class="py-2 px-3 text-sm border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        </div>

                        <div class="flex items-center space-x-2 text-xs text-slate-500">
                            <span>Hasta:</span>
                            <input type="date" name="to" value="{{ request('to') }}" 
                                   class="py-2 px-3 text-sm border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        </div>

                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">
                            Filtrar
                        </button>

                        @if (request()->hasAny(['search', 'from', 'to']))
                            <a href="{{ route('sales.index') }}" class="text-slate-500 hover:text-slate-800 text-sm font-medium">Limpiar</a>
                        @endif
                    </form>
                </div>

                <!-- Tabla de Ventas -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-900 text-white">
                            <tr>
                                <th class="px-4 py-3.5 text-left text-xs uppercase font-bold tracking-wider">Factura</th>
                                <th class="px-4 py-3.5 text-left text-xs uppercase font-bold tracking-wider">Fecha</th>
                                <th class="px-4 py-3.5 text-left text-xs uppercase font-bold tracking-wider">Cliente</th>
                                <th class="px-4 py-3.5 text-left text-xs uppercase font-bold tracking-wider">Vendedor</th>
                                <th class="px-4 py-3.5 text-left text-xs uppercase font-bold tracking-wider">Total</th>
                                <th class="px-4 py-3.5 text-center text-xs uppercase font-bold tracking-wider">Estado</th>
                                <th class="px-4 py-3.5 text-center text-xs uppercase font-bold tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($sales as $sale)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 text-sm font-mono font-bold text-slate-800">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        {{ $sale->sale_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">
                                        {{ $sale->client->name ?? 'Cliente Desconocido' }} {{ $sale->client->first_surname ?? '' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        {{ $sale->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold text-emerald-700">
                                        ${{ number_format($sale->total, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm">
                                        @if ($sale->status === 'pagada')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                Pagada
                                            </span>
                                        @elseif ($sale->status === 'anulada')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                Anulada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm space-x-2">
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 font-bold transition">
                                            <i class="fa-regular fa-file-lines mr-1"></i> Ver
                                        </a>

                                        @if ($sale->status !== 'anulada')
                                            @can('eliminar-ventas')
                                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline-block"
                                                      onsubmit="return confirm('¿Seguro que deseas anular esta venta? El stock de los productos será devuelto automáticamente al inventario.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold transition">
                                                        <i class="fa-solid fa-ban mr-1"></i> Anular
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                        No hay registros de ventas que coincidan con la búsqueda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if ($sales->hasPages())
                    <div class="pt-5 border-t border-slate-100">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
