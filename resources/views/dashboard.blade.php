<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Panel de Control (Dashboard)') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-slate-500 font-semibold">Rol activo:</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <i class="fa-solid fa-shield-halved mr-1.5 text-xs"></i>
                    {{ $roles->isEmpty() ? 'Sin rol asignado' : ucfirst($roles->first()) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Banner de Bienvenida -->
        <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 rounded-2xl shadow-lg p-6 sm:p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 border border-emerald-900/50">
            <div>
                <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider mb-2 inline-block">
                    VetPets ERP &bull; Centro de Mando
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">¡Bienvenido de nuevo, {{ $user->name }}!</h1>
                <p class="text-slate-300 text-sm mt-1 max-w-2xl">
                    Monitoreo en tiempo real de inventarios, transacciones de venta, compras a proveedores y abastecimiento.
                </p>
            </div>

            <div class="flex flex-wrap gap-2 shrink-0">
                @can('crear-ventas')
                    <a href="{{ route('sales.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>+ Venta POS</span>
                    </a>
                @endcan

                @can('crear-compras')
                    <a href="{{ route('purchases.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-cart-flatbed"></i>
                        <span>+ Orden Compra</span>
                    </a>
                @endcan
            </div>
        </div>

        <!-- Grid de Tarjetas KPIs Dinámicos -->
        @if ($stats->isNotEmpty())
            <div>
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-500 mb-3 px-1">Indicadores Clave del Negocio (KPIs)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($stats as $stat)
                        @php
                            $colorMap = [
                                'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'badge' => 'text-emerald-700'],
                                'rose'    => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'badge' => 'text-rose-700 font-bold'],
                                'blue'    => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'badge' => 'text-blue-700'],
                                'amber'   => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'badge' => 'text-amber-700'],
                                'purple'  => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'badge' => 'text-purple-700'],
                                'indigo'  => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'badge' => 'text-indigo-700'],
                                'teal'    => ['bg' => 'bg-teal-50', 'text' => 'text-teal-600', 'badge' => 'text-teal-700'],
                            ];
                            $c = $colorMap[$stat['color']] ?? $colorMap['emerald'];
                        @endphp
                        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $stat['label'] }}</span>
                                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $stat['value'] }}</h3>
                                </div>
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg shrink-0 {{ $c['bg'] }} {{ $c['text'] }}">
                                    <i class="fa-solid {{ $stat['icon'] }}"></i>
                                </div>
                            </div>
                            <span class="block text-xs font-medium mt-3 {{ $c['badge'] }}">
                                <i class="fa-solid fa-chart-simple mr-1"></i> {{ $stat['badge'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Alerta de Reabastecimiento Crítico (Stock <= Mínimo) -->
        @if ($criticalStockProducts->isNotEmpty())
            <div class="bg-white rounded-2xl border border-rose-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-rose-100 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-lg">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Alerta de Reabastecimiento: Stock Crítico (≤ Mínimo)</h3>
                            <p class="text-xs text-slate-500">Medicamentos e insumos que requieren emisión prioritaria de Orden de Compra</p>
                        </div>
                    </div>
                    @can('crear-compras')
                        <a href="{{ route('purchases.create') }}"
                           class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-3.5 rounded-xl text-xs transition flex items-center space-x-1.5 shadow-sm">
                            <i class="fa-solid fa-cart-plus"></i>
                            <span>Emitir Orden de Compra</span>
                        </a>
                    @endcan
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-rose-50/60 text-slate-700 uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">Producto</th>
                                <th class="px-4 py-3 text-left">Categoría</th>
                                <th class="px-4 py-3 text-center">Stock Actual</th>
                                <th class="px-4 py-3 text-center">Stock Mínimo</th>
                                <th class="px-4 py-3 text-right">Costo Estimado</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($criticalStockProducts as $prod)
                                <tr class="hover:bg-rose-50/30 transition">
                                    <td class="px-4 py-2.5 font-bold text-slate-900">
                                        {{ $prod->name }}
                                    </td>
                                    <td class="px-4 py-2.5 text-xs text-slate-600">
                                        {{ $prod->category?->name ?? 'General' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center font-bold text-rose-600">
                                        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-xs">
                                            {{ $prod->current_stock }} unid.
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center text-xs font-semibold text-slate-600">
                                        {{ $prod->minimum_stock }} unid.
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-xs text-slate-700">
                                        ${{ number_format($prod->cost_price ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="text-[11px] font-extrabold uppercase text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded">
                                            Agotándose
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Sección de Top 5 Productos Más Vendidos del Mes -->
        @if ($user->can('ver-ventas') && $topProducts->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-lg">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Ranking: Top 5 Productos Más Vendidos</h3>
                            <p class="text-xs text-slate-500">Volumen de ventas registrado durante el mes en curso</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-center w-16">Puesto</th>
                                <th class="px-4 py-3 text-left">Producto</th>
                                <th class="px-4 py-3 text-right">Precio Venta</th>
                                <th class="px-4 py-3 text-center">Total Unidades Vendidas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($topProducts as $index => $item)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 text-center">
                                        @if ($index === 0)
                                            <span class="w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black inline-flex items-center justify-center text-xs shadow-sm">1</span>
                                        @elseif ($index === 1)
                                            <span class="w-7 h-7 rounded-full bg-slate-300 text-slate-900 font-black inline-flex items-center justify-center text-xs shadow-sm">2</span>
                                        @elseif ($index === 2)
                                            <span class="w-7 h-7 rounded-full bg-amber-700 text-white font-black inline-flex items-center justify-center text-xs shadow-sm">3</span>
                                        @else
                                            <span class="text-slate-500 font-bold text-xs">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-900">
                                        {{ $item->product->name ?? 'Producto eliminado del catálogo' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-700 font-medium">
                                        ${{ number_format($item->product->sale_price ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="bg-emerald-100 text-emerald-800 text-xs px-3 py-1 rounded-full font-black">
                                            {{ $item->total_sold }} unidades
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Accesos Rápidos a Módulos (Permisos de Spatie) --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-xs uppercase font-bold tracking-wider text-slate-500 mb-4 px-1">Acciones y Accesos Rápidos del ERP</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @can('crear-ventas')
                    <a href="{{ route('sales.create') }}" class="p-3 rounded-xl border border-slate-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                        <i class="fa-solid fa-cash-register text-xl text-slate-600 group-hover:text-emerald-600 mb-1"></i>
                        <span class="block text-[11px] font-bold text-slate-800">Nueva Venta</span>
                    </a>
                @endcan

                @can('crear-compras')
                    <a href="{{ route('purchases.create') }}" class="p-3 rounded-xl border border-slate-100 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 text-center transition group">
                        <i class="fa-solid fa-cart-flatbed text-xl text-slate-600 group-hover:text-indigo-600 mb-1"></i>
                        <span class="block text-[11px] font-bold text-slate-800">Nueva Compra</span>
                    </a>
                @endcan

                @can('crear-proveedores')
                    <a href="{{ route('providers.create') }}" class="p-3 rounded-xl border border-slate-100 bg-slate-50 hover:bg-teal-50 hover:border-teal-200 text-center transition group">
                        <i class="fa-solid fa-building text-xl text-slate-600 group-hover:text-teal-600 mb-1"></i>
                        <span class="block text-[11px] font-bold text-slate-800">Nuevo Proveedor</span>
                    </a>
                @endcan

                @can('crear-clientes')
                    <a href="{{ route('clients.create') }}" class="p-3 rounded-xl border border-slate-100 bg-slate-50 hover:bg-blue-50 hover:border-blue-200 text-center transition group">
                        <i class="fa-solid fa-user-plus text-xl text-slate-600 group-hover:text-blue-600 mb-1"></i>
                        <span class="block text-[11px] font-bold text-slate-800">Nuevo Cliente</span>
                    </a>
                @endcan

                @can('crear-productos')
                    <a href="{{ route('products.create') }}" class="p-3 rounded-xl border border-slate-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                        <i class="fa-solid fa-box-open text-xl text-slate-600 group-hover:text-emerald-600 mb-1"></i>
                        <span class="block text-[11px] font-bold text-slate-800">Nuevo Producto</span>
                    </a>
                @endcan

                @can('crear-categorias')
                    <a href="{{ route('categories.create') }}" class="p-3 rounded-xl border border-slate-100 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 text-center transition group">
                        <i class="fa-solid fa-tags text-xl text-slate-600 group-hover:text-amber-600 mb-1"></i>
                        <span class="block text-[11px] font-bold text-slate-800">Nueva Categoría</span>
                    </a>
                @endcan
            </div>
        </div>

    </div>
</x-app-layout>
