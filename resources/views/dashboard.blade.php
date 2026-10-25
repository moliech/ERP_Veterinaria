<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Panel de Control</span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        En vivo
                    </span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Resumen comercial, compras y alertas operativas de <strong class="text-slate-800">{{ $company->company_name ?? 'VetPets ERP' }}</strong>
                </p>
            </div>

            <!-- Accesos Rápidos con contraste sólido -->
            <div class="flex flex-wrap items-center gap-2">
                @can('crear-ventas')
                    <a href="{{ route('sales.create') }}"
                       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs transition shadow-sm">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>Nueva Venta</span>
                    </a>
                @endcan
                @can('crear-compras')
                    <a href="{{ route('purchases.create') }}"
                       class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold px-3.5 py-2 rounded-xl text-xs transition shadow-sm">
                        <i class="fa-solid fa-cart-plus"></i>
                        <span>Nueva Compra</span>
                    </a>
                @endcan
                @can('crear-productos')
                    <a href="{{ route('products.create') }}"
                       class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold border border-slate-300 px-3 py-2 rounded-xl text-xs transition shadow-sm">
                        <i class="fa-solid fa-plus text-slate-400"></i>
                        <span>Producto</span>
                    </a>
                @endcan
                @can('crear-categorias')
                    <a href="{{ route('categories.create') }}"
                       class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold border border-slate-300 px-3 py-2 rounded-xl text-xs transition shadow-sm">
                        <i class="fa-solid fa-tags text-slate-400"></i>
                        <span>Categoría</span>
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Banner Informativo del Usuario y Rol Activo -->
            <div class="bg-slate-900 rounded-2xl p-5 text-white shadow-md border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center font-black text-emerald-400 text-lg border border-slate-700 shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-sm font-bold flex items-center gap-2">
                            <span>Bienvenido, {{ auth()->user()->name }}</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-slate-800 text-emerald-400 border border-slate-700">
                                {{ auth()->user()->email }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 mt-0.5">
                            Roles asignados: <span class="capitalize text-slate-100 font-medium">{{ $roles->isEmpty() ? 'Sin rol' : $roles->join(', ') }}</span>
                            @if ($activeRole)
                                · Rol activo en sesión: <strong class="text-emerald-400 font-bold capitalize">{{ $activeRole }}</strong>
                            @endif
                        </p>
                    </div>
                </div>

                @if ($roles->count() > 1)
                    <div class="shrink-0">
                        <a href="{{ route('active-role.edit') }}"
                           class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold px-3 py-1.5 rounded-xl text-xs transition border border-slate-700">
                            <i class="fa-solid fa-arrows-rotate text-emerald-400"></i>
                            <span>Cambiar Rol Activo</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Tarjetas de KPIs 100% Sólidas y Visibles -->
            @if ($stats->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($stats as $stat)
                        @php
                            $styles = [
                                'emerald' => [
                                    'card' => 'bg-white border-l-4 border-l-emerald-600 border-slate-200',
                                    'icon' => 'bg-emerald-600 text-white',
                                    'val'  => 'text-slate-900',
                                ],
                                'indigo' => [
                                    'card' => 'bg-white border-l-4 border-l-indigo-600 border-slate-200',
                                    'icon' => 'bg-indigo-600 text-white',
                                    'val'  => 'text-slate-900',
                                ],
                                'amber' => [
                                    'card' => 'bg-white border-l-4 border-l-amber-500 border-slate-200',
                                    'icon' => 'bg-amber-500 text-white',
                                    'val'  => 'text-slate-900',
                                ],
                                'sky' => [
                                    'card' => 'bg-white border-l-4 border-l-sky-600 border-slate-200',
                                    'icon' => 'bg-sky-600 text-white',
                                    'val'  => 'text-slate-900',
                                ],
                            ];
                            $colorKey = $stat['color'] ?? 'emerald';
                            $cur = $styles[$colorKey] ?? $styles['emerald'];
                        @endphp
                        <div class="bg-white rounded-2xl p-5 border shadow-sm transition hover:shadow-md flex flex-col justify-between {{ $cur['card'] }}">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">
                                        {{ $stat['label'] }}
                                    </span>
                                    <div class="text-2xl font-black {{ $cur['val'] }} mt-1 tracking-tight font-mono">
                                        {{ $stat['value'] }}
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-xl {{ $cur['icon'] }} flex items-center justify-center text-sm shadow-sm shrink-0">
                                    <i class="{{ $stat['icon'] ?? 'fa-solid fa-chart-line' }}"></i>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 font-medium flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-info text-[10px] text-slate-400"></i>
                                <span>{{ $stat['subtext'] ?? 'Métrica calculada en tiempo real' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Fila Principal: Top 5 Productos y Productos para Reabastecer -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Top 5 Productos Más Vendidos -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                                    <i class="fa-solid fa-fire text-amber-500"></i>
                                    <span>Top 5 Productos Más Vendidos</span>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Mayor rotación de inventario durante el mes</p>
                            </div>
                            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                                Este mes
                            </span>
                        </div>

                        @if ($topProducts->isNotEmpty())
                            @php
                                $maxSold = max(1, $topProducts->first()->total_sold ?? 1);
                            @endphp
                            <div class="divide-y divide-slate-100 mt-2">
                                @foreach ($topProducts as $i => $item)
                                    @php
                                        $percent = round(($item->total_sold / $maxSold) * 100);
                                        $medalColors = [
                                            0 => 'bg-amber-100 text-amber-800 border-amber-300 font-black',
                                            1 => 'bg-slate-100 text-slate-700 border-slate-300 font-bold',
                                            2 => 'bg-orange-100 text-orange-800 border-orange-300 font-bold',
                                        ];
                                    @endphp
                                    <div class="py-3 flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-lg text-xs flex items-center justify-center shrink-0 border {{ $medalColors[$i] ?? 'bg-slate-50 text-slate-500 border-slate-200' }}">
                                            {{ $i + 1 }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="font-bold text-slate-900 truncate">{{ $item->product?->name ?? 'Producto no disponible' }}</span>
                                                <span class="font-mono font-bold text-emerald-700 shrink-0 ml-2">{{ $item->total_sold }} unid.</span>
                                            </div>
                                            <!-- Barra de progreso con buen contraste -->
                                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200/60">
                                                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $percent }}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-8 text-center text-xs text-slate-400">
                                <i class="fa-solid fa-basket-shopping text-3xl mb-2 text-slate-300 block"></i>
                                No se registran ventas facturadas en el mes actual.
                            </div>
                        @endif
                    </div>

                    @can('ver-ventas')
                        <div class="pt-3 border-t border-slate-100 text-right">
                            <a href="{{ route('sales.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                                <span>Ver historial de ventas</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    @endcan
                </div>

                <!-- Productos para Reabastecer (Stock Crítico) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                                    <span>Alerta de Reabastecimiento</span>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Productos en stock mínimo o crítico (≤ 5 unid.)</p>
                            </div>
                            @if($lowStock->isNotEmpty())
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                    {{ $lowStock->count() }} por reordenar
                                </span>
                            @endif
                        </div>

                        @if ($lowStock->isNotEmpty())
                            <div class="divide-y divide-slate-100 mt-2">
                                @foreach ($lowStock as $prod)
                                    @php
                                        $isCritical = $prod->current_stock <= 2;
                                    @endphp
                                    <div class="py-2.5 flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <span class="font-bold text-xs text-slate-900 block truncate">{{ $prod->name }}</span>
                                            <span class="text-[10px] text-slate-500 font-medium">
                                                Línea: {{ $prod->category?->name ?? 'General' }} · Mínimo sugerido: {{ $prod->minimum_stock }} unid.
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold {{ $isCritical ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                                {{ $prod->current_stock }} unid.
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-8 text-center text-xs text-emerald-600">
                                <i class="fa-solid fa-circle-check text-3xl mb-2 text-emerald-400 block"></i>
                                Todo el catálogo cuenta con existencias superiores al stock de seguridad.
                            </div>
                        @endif
                    </div>

                    @can('crear-compras')
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-500">¿Requiere mercancía urgente?</span>
                            <a href="{{ route('purchases.create') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                                <i class="fa-solid fa-cart-plus"></i>
                                <span>Crear Orden de Compra</span>
                            </a>
                        </div>
                    @endcan
                </div>

            </div>

            <!-- Fila Secundaria: Lotes Próximos a Vencer + Estado de Órdenes de Compra -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Widget de Lotes y Vencimientos -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                                <span>Control de Lotes y Vencimientos Próximos</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Seguimiento de lotes perecederos para prevenir mermas</p>
                        </div>
                        @can('ver-productos')
                            <a href="{{ route('pricing.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                Gestionar Tarifas &rarr;
                            </a>
                        @endcan
                    </div>

                    @if (isset($expiringLots) && $expiringLots->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            @foreach ($expiringLots as $lot)
                                @php
                                    $status = $lot->expirationStatus();
                                    $daysLeft = (int) now()->startOfDay()->diffInDays($lot->expiration_date?->startOfDay(), false);
                                @endphp
                                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-bold text-xs text-slate-900 truncate">{{ $lot->name }}</div>
                                        <div class="text-[11px] font-mono text-slate-600 mt-0.5">
                                            Lote: <strong class="text-slate-800">{{ $lot->batch_code ?? 'S/L' }}</strong>
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            Vence: {{ $lot->expiration_date?->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border shrink-0 {{ $status['badge'] }}">
                                        {{ $daysLeft < 0 ? 'Vencido' : ($daysLeft . ' días') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-6 text-center text-xs text-slate-400">
                            <i class="fa-solid fa-shield-heart text-2xl mb-1 text-slate-300 block"></i>
                            No hay lotes con vencimiento próximo en los siguientes 60 días.
                        </div>
                    @endif
                </div>

                <!-- Resumen de Órdenes de Compra (Tarjeta 100% Sólida y Nítida) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-truck-ramp-box text-emerald-600"></i>
                                <span>Órdenes de Compra</span>
                            </h3>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 border border-slate-200">
                                Abastecimiento
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Control de pedidos a proveedores con recepción física parcial o total.
                        </p>

                        <div class="my-4 p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-500 font-medium block">Órdenes por recibir:</span>
                                <span class="text-2xl font-black text-slate-900 font-mono">{{ $pendingPurchasesCount ?? 0 }}</span>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm shadow-sm">
                                <i class="fa-solid fa-boxes-packing"></i>
                            </div>
                        </div>
                    </div>

                    @can('ver-compras')
                        <a href="{{ route('purchases.index') }}"
                           class="w-full text-center bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition shadow-sm">
                            Ir a Órdenes de Compra &rarr;
                        </a>
                    @endcan
                </div>

            </div>

            <!-- Accesos a Módulos del Sistema -->
            <div>
                <div class="flex items-center justify-between mb-3 px-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        Módulos Disponibles en su Perfil
                    </span>
                    <span class="text-xs text-slate-400">Permisos efectivos por Rol Activo</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @forelse ($modules as $module)
                        <a href="{{ route($module['route']) }}"
                           class="bg-white rounded-2xl p-5 border border-slate-200 hover:border-emerald-500 hover:shadow-md transition group flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-bold text-sm text-slate-900 group-hover:text-emerald-700 transition">
                                        {{ $module['title'] }}
                                    </h4>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-400 group-hover:text-emerald-600 transition"></i>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    {{ $module['description'] }}
                                </p>
                            </div>
                            <div class="mt-4 pt-2.5 border-t border-slate-100 text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                                <span>Abrir módulo</span>
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                            <p class="font-medium text-slate-900">No tienes módulos disponibles en este rol.</p>
                            <p class="mt-1 text-xs text-slate-500">Contacta al administrador o cambia tu rol activo.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
