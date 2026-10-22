<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Módulo de Compras y Reabastecimiento') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Gestión de órdenes de compra, control de recepciones parciales y abastecimiento</p>
            </div>

            @can('crear-compras')
                <a href="{{ route('purchases.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm text-sm transition flex items-center space-x-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>+ Nueva Orden de Compra</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs Rápidos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Compras del Mes</span>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">
                            ${{ number_format((float) $totalMonth, 2, ',', '.') }}
                        </div>
                        <span class="text-[11px] text-emerald-600 font-semibold"><i class="fa-solid fa-calendar-check mr-1"></i>Mes actual</span>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Órdenes por Recibir</span>
                        <div class="text-2xl font-extrabold text-indigo-600 mt-1">
                            {{ $pendingCount }}
                        </div>
                        <span class="text-[11px] text-indigo-500 font-semibold"><i class="fa-solid fa-truck-ramp-box mr-1"></i>Pendientes o parciales</span>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Control de Calidad</span>
                        <div class="text-2xl font-extrabold text-slate-700 mt-1">
                            IVA 19%
                        </div>
                        <span class="text-[11px] text-slate-500 font-semibold"><i class="fa-solid fa-shield-halved mr-1"></i>Trazabilidad tributaria</span>
                    </div>
                    <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">

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

                <!-- Filtros -->
                <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Buscar por N° OC o Proveedor..."
                               class="w-full px-4 py-2 border border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Todos los Estados</option>
                            <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="parcial" {{ request('status') === 'parcial' ? 'selected' : '' }}>Recepción Parcial</option>
                            <option value="recibida" {{ request('status') === 'recibida' ? 'selected' : '' }}>Recibida Completa</option>
                            <option value="anulada" {{ request('status') === 'anulada' ? 'selected' : '' }}>Anulada</option>
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                            Filtrar
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'from', 'to']))
                            <a href="{{ route('purchases.index') }}" class="px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-600 hover:bg-slate-100 flex items-center justify-center">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Tabla de Compras -->
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">N° Orden</th>
                                <th class="px-4 py-3 text-left">Proveedor / Laboratorio</th>
                                <th class="px-4 py-3 text-left">Fecha</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-right">Total (con IVA)</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($purchases as $purchase)
                                <tr class="hover:bg-slate-50/80 transition {{ $purchase->trashed() ? 'opacity-60 bg-slate-50/40' : '' }}">
                                    <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                                        {{ $purchase->purchase_number }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-800 font-medium">
                                        {{ $purchase->provider?->company_name ?? 'Proveedor no disponible' }}
                                        <div class="text-[11px] text-slate-400">NIT: {{ $purchase->provider?->nit ?? 'N/D' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">
                                        {{ $purchase->purchase_date?->format('d/m/Y') }}
                                        <div class="text-[10px] text-slate-400">Por: {{ $purchase->user?->name ?? 'Usuario' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @php
                                            $badges = [
                                                'pendiente' => 'bg-amber-100 text-amber-800 border-amber-300',
                                                'parcial'   => 'bg-blue-100 text-blue-800 border-blue-300',
                                                'recibida'  => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                                'anulada'   => 'bg-rose-100 text-rose-800 border-rose-300',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badges[$purchase->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                        ${{ number_format((float) $purchase->total, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('purchases.show', $purchase) }}"
                                           class="inline-flex items-center text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg transition">
                                            <i class="fa-solid fa-eye mr-1"></i> Ver
                                        </a>

                                        <a href="{{ route('purchases.pdf', $purchase) }}" target="_blank"
                                           class="inline-flex items-center text-xs font-semibold text-rose-700 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition">
                                            <i class="fa-solid fa-file-pdf mr-1"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                        No se encontraron órdenes de compra registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $purchases->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
