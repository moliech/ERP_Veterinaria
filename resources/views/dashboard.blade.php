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
                    Monitoreo en tiempo real de inventarios, transacciones de venta e indicadores clave para la toma de decisiones.
                </p>
            </div>

            @can('crear-ventas')
                <a href="{{ route('sales.create') }}" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition whitespace-nowrap flex items-center space-x-2 shrink-0">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>+ Registrar Venta POS</span>
                </a>
            @endcan
        </div>

        <!-- Grid de Tarjetas KPIs Dinámicos -->
        @if ($stats->isNotEmpty())
            <div>
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-500 mb-3 px-1">Indicadores Clave del Negocio (KPIs)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach ($stats as $stat)
                        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $stat['label'] }}</span>
                                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $stat['value'] }}</h3>
                                </div>
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0
                                    {{ $stat['color'] === 'emerald' ? 'bg-emerald-50 text-emerald-600' : '' }}
                                    {{ $stat['color'] === 'rose' ? 'bg-rose-50 text-rose-600' : '' }}
                                    {{ $stat['color'] === 'blue' ? 'bg-blue-50 text-blue-600' : '' }}
                                    {{ $stat['color'] === 'amber' ? 'bg-amber-50 text-amber-600' : '' }}">
                                    <i class="fa-solid {{ $stat['icon'] }}"></i>
                                </div>
                            </div>
                            <span class="block text-xs font-medium mt-3 
                                {{ $stat['color'] === 'emerald' ? 'text-emerald-700' : '' }}
                                {{ $stat['color'] === 'rose' ? 'text-rose-700 font-bold' : '' }}
                                {{ $stat['color'] === 'blue' ? 'text-blue-700' : '' }}
                                {{ $stat['color'] === 'amber' ? 'text-amber-700' : '' }}">
                                <i class="fa-solid fa-chart-simple mr-1"></i> {{ $stat['badge'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Sección de Top 5 Productos Más Vendidos del Mes -->
        @if ($user->can('ver-ventas'))
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center font-bold">
                            <i class="fa-solid fa-trophy text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 leading-tight">Top 5 Productos Más Vendidos (Mes Actual)</h3>
                            <span class="text-xs text-slate-500">Clasificación acumulada por unidades despachadas en el punto de venta</span>
                        </div>
                    </div>
                    <span class="text-xs bg-slate-100 text-slate-700 px-3 py-1 rounded-full font-bold">
                        {{ now()->translatedFormat('F Y') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-900 text-white text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-center w-16"># Ranking</th>
                                <th class="px-4 py-3 text-left">Producto / Insumo</th>
                                <th class="px-4 py-3 text-right">Precio de Venta</th>
                                <th class="px-4 py-3 text-center">Unidades Vendidas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-sm">
                            @forelse ($topProducts as $index => $item)
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
                                        ${{ number_format($item->product->sale_price ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="bg-emerald-100 text-emerald-800 text-xs px-3 py-1 rounded-full font-black">
                                            {{ $item->total_sold }} unidades
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                        Aún no se han registrado transacciones de venta este mes para generar el ranking.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Accesos Rápidos a Módulos (Con directivas de Permisos @can) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-xs uppercase font-bold tracking-wider text-slate-500 mb-4 px-1">Acciones y Accesos Rápidos</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @can('crear-ventas')
                    <a href="{{ route('sales.create') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                        <i class="fa-solid fa-cash-register text-2xl text-slate-600 group-hover:text-emerald-600 mb-2"></i>
                        <span class="block text-xs font-bold text-slate-800">Nueva Venta POS</span>
                    </a>
                @endcan

                @can('crear-clientes')
                    <a href="{{ route('clients.create') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                        <i class="fa-solid fa-user-plus text-2xl text-slate-600 group-hover:text-emerald-600 mb-2"></i>
                        <span class="block text-xs font-bold text-slate-800">Nuevo Cliente</span>
                    </a>
                @endcan

                @can('crear-productos')
                    <a href="{{ route('products.create') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                        <i class="fa-solid fa-box-open text-2xl text-slate-600 group-hover:text-emerald-600 mb-2"></i>
                        <span class="block text-xs font-bold text-slate-800">Nuevo Producto</span>
                    </a>
                @endcan

                @can('crear-categorias')
                    <a href="{{ route('categories.create') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                        <i class="fa-solid fa-tags text-2xl text-slate-600 group-hover:text-emerald-600 mb-2"></i>
                        <span class="block text-xs font-bold text-slate-800">Nueva Categoría</span>
                    </a>
                @endcan
            </div>
        </div>

    </div>
</x-app-layout>