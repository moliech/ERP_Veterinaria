<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $corp = $company ?? \App\Models\CompanySetting::current();
        $colorMeta = $corp->colorMeta();
        $accentHex = $corp->primaryColorHex();
        $mods = $enabledModules ?? \App\Models\SystemModule::enabledMap();
        $activeRoleObj = Auth::check() ? Auth::user()->activeRole() : null;
    @endphp

    <title>{{ $corp->company_name }} — ERP</title>

    <!-- Tipografía Figtree -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Scripts & Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Paleta Institucional Dinámica (Identidad Corporativa global en todos los módulos) -->
    <style>
        :root {
            --corp-50: {{ $colorMeta['50'] ?? '#ecfdf5' }};
            --corp-100: {{ $colorMeta['100'] ?? '#d1fae5' }};
            --corp-200: {{ $colorMeta['200'] ?? '#a7f3d0' }};
            --corp-300: {{ $colorMeta['300'] ?? '#6ee7b7' }};
            --corp-400: {{ $colorMeta['400'] ?? '#34d399' }};
            --corp-500: {{ $colorMeta['500'] ?? '#10b981' }};
            --corp-600: {{ $colorMeta['600'] ?? '#059669' }};
            --corp-700: {{ $colorMeta['700'] ?? '#047857' }};
            --corp-800: {{ $colorMeta['800'] ?? '#065f46' }};
            --corp-900: {{ $colorMeta['900'] ?? '#064e3b' }};
        }
        .bg-emerald-50 { background-color: var(--corp-50) !important; }
        .bg-emerald-100 { background-color: var(--corp-100) !important; }
        .bg-emerald-500 { background-color: var(--corp-500) !important; }
        .bg-emerald-600 { background-color: var(--corp-600) !important; }
        .bg-emerald-700 { background-color: var(--corp-700) !important; }
        .hover\:bg-emerald-100:hover { background-color: var(--corp-100) !important; }
        .hover\:bg-emerald-700:hover { background-color: var(--corp-700) !important; }
        .text-emerald-100 { color: var(--corp-100) !important; }
        .text-emerald-400 { color: var(--corp-400) !important; }
        .text-emerald-500 { color: var(--corp-500) !important; }
        .text-emerald-600 { color: var(--corp-600) !important; }
        .text-emerald-700 { color: var(--corp-700) !important; }
        .text-emerald-800 { color: var(--corp-800) !important; }
        .border-emerald-200 { border-color: var(--corp-200) !important; }
        .border-emerald-300 { border-color: var(--corp-300) !important; }
        .border-emerald-500 { border-color: var(--corp-500) !important; }
        .border-emerald-600 { border-color: var(--corp-600) !important; }
        .focus\:border-emerald-500:focus { border-color: var(--corp-500) !important; }
        .focus\:ring-emerald-500:focus { --tw-ring-color: var(--corp-500) !important; }
    </style>
</head>

<body class="font-sans antialiased bg-slate-100 text-slate-800 min-h-screen">
    <div class="flex min-h-screen">

        <!-- Sidebar Lateral Izquierdo -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between fixed inset-y-0 left-0 z-50 shadow-xl overflow-y-auto">
            <div>
                <!-- Logo Header Sidebar (Identidad Corporativa Dinámica) -->
                <div class="h-20 flex items-center px-5 border-b border-slate-800 bg-slate-950">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md shrink-0" style="background-color: {{ $accentHex }};">
                            <i class="{{ $corp->icon_class }} text-white text-lg"></i>
                        </div>
                        <div class="truncate">
                            <span class="text-base font-extrabold text-white tracking-wide block leading-tight truncate">{{ $corp->company_name }}</span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold block mt-0.5 truncate">{{ $corp->business_subtitle }}</span>
                        </div>
                    </a>
                </div>

                <!-- Menú de Módulos Categorizados -->
                <nav class="mt-5 px-4 space-y-5 pb-4">

                    <!-- Categoría 1: General -->
                    <div>
                        <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">General</span>
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('dashboard') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            @if(request()->routeIs('dashboard')) style="background-color: {{ $accentHex }};" @endif>
                            <i class="fa-solid fa-chart-line w-6 text-center mr-2 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                            Dashboard
                        </a>
                    </div>

                    <!-- Categoría 2: Comercial & POS -->
                    <div>
                        <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Comercial & POS</span>
                        <div class="space-y-1">
                            @if($mods['clients'] ?? true)
                                @can('ver-clientes')
                                <a href="{{ route('clients.index') }}"
                                    class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('clients.*') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                    @if(request()->routeIs('clients.*')) style="background-color: {{ $accentHex }};" @endif>
                                    <i class="fa-solid fa-users w-6 text-center mr-2 {{ request()->routeIs('clients.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                    Clientes
                                </a>
                                @endcan
                            @endif

                            @if($mods['sales'] ?? true)
                                @can('ver-ventas')
                                <a href="{{ route('sales.index') }}"
                                    class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('sales.*') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                    @if(request()->routeIs('sales.*')) style="background-color: {{ $accentHex }};" @endif>
                                    <i class="fa-solid fa-cash-register w-6 text-center mr-2 {{ request()->routeIs('sales.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                    Ventas POS
                                </a>
                                @endcan
                            @endif
                        </div>
                    </div>

                    <!-- Categoría 3: Inventario & Abastecimiento -->
                    <div>
                        <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Inventario & Compras</span>
                        <div class="space-y-1">
                            @if($mods['products'] ?? true)
                                @can('ver-productos')
                                <a href="{{ route('products.index') }}"
                                    class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('products.*') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                    @if(request()->routeIs('products.*')) style="background-color: {{ $accentHex }};" @endif>
                                    <i class="fa-solid fa-boxes-stacked w-6 text-center mr-2 {{ request()->routeIs('products.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                    Inventario y Lotes
                                </a>
                                @endcan
                            @endif

                            @if($mods['pricing'] ?? true)
                                @can('ver-productos')
                                <a href="{{ route('pricing.index') }}"
                                    class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('pricing.*') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                    @if(request()->routeIs('pricing.*')) style="background-color: {{ $accentHex }};" @endif>
                                    <i class="fa-solid fa-file-invoice-dollar w-6 text-center mr-2 {{ request()->routeIs('pricing.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                    Tarifas e IVA
                                </a>
                                @endcan
                            @endif

                            @if($mods['categories'] ?? true)
                                @can('ver-categorias')
                                <a href="{{ route('categories.index') }}"
                                    class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('categories.*') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                    @if(request()->routeIs('categories.*')) style="background-color: {{ $accentHex }};" @endif>
                                    <i class="fa-solid fa-tags w-6 text-center mr-2 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                    Categorías
                                </a>
                                @endcan
                            @endif

                            @if($mods['providers'] ?? true)
                                @can('ver-proveedores')
                                <a href="{{ route('providers.index') }}"
                                    class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('providers.*') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                    @if(request()->routeIs('providers.*')) style="background-color: {{ $accentHex }};" @endif>
                                    <i class="fa-solid fa-truck-field w-6 text-center mr-2 {{ request()->routeIs('providers.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                    Proveedores
                                </a>
                                @endcan
                            @endif

                            @if($mods['purchases'] ?? true)
                                @can('ver-compras')
                                <a href="{{ route('purchases.index') }}"
                                    class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('purchases.*') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                    @if(request()->routeIs('purchases.*')) style="background-color: {{ $accentHex }};" @endif>
                                    <i class="fa-solid fa-cart-flatbed w-6 text-center mr-2 {{ request()->routeIs('purchases.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                    Órdenes de Compra
                                </a>
                                @endcan
                            @endif
                        </div>
                    </div>

                    <!-- Categoría 4: Configuración & Administración -->
                    @canany(['ver-usuarios', 'ver-roles', 'ver-permisos'])
                    <div>
                        <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Configuración</span>
                        <div class="space-y-1">
                            @php
                                $isSecurityRoute = request()->routeIs('settings.index', 'settings.users.*', 'settings.roles.*', 'settings.permissions.*');
                            @endphp
                            <a href="{{ route('settings.index') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ $isSecurityRoute ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                @if($isSecurityRoute) style="background-color: {{ $accentHex }};" @endif>
                                <i class="fa-solid fa-shield-halved w-6 text-center mr-2 {{ $isSecurityRoute ? 'text-white' : 'text-slate-400' }}"></i>
                                Seguridad y Accesos
                            </a>

                            <a href="{{ route('settings.company.edit') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('settings.company.*') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                @if(request()->routeIs('settings.company.*')) style="background-color: {{ $accentHex }};" @endif>
                                <i class="fa-solid fa-building-columns w-6 text-center mr-2 {{ request()->routeIs('settings.company.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                Identidad Corporativa
                            </a>

                            <a href="{{ route('settings.modules.index') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('settings.modules.*') ? 'text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                @if(request()->routeIs('settings.modules.*')) style="background-color: {{ $accentHex }};" @endif>
                                <i class="fa-solid fa-cubes-stacked w-6 text-center mr-2 {{ request()->routeIs('settings.modules.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                Gestión de Módulos
                            </a>
                        </div>
                    </div>
                    @endcanany

                </nav>
            </div>

            <!-- Footer Usuario y Rol Activo en Sidebar -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/60 space-y-2.5">
                @if ($activeRoleObj)
                    <div class="flex items-center justify-between bg-slate-900 px-3 py-1.5 rounded-lg border border-slate-800 text-xs">
                        <span class="text-slate-400">Rol activo: <strong class="text-white capitalize">{{ $activeRoleObj->name }}</strong></span>
                        @if (Auth::user()->hasMultipleRoles())
                            <a href="{{ route('active-role.edit') }}" class="text-emerald-400 hover:underline font-semibold">
                                Cambiar
                            </a>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-9 h-9 text-white rounded-lg flex items-center justify-center font-bold text-xs shrink-0" style="background-color: {{ $accentHex }};">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="truncate">
                            <span class="block text-xs font-bold text-white truncate">{{ Auth::user()->name }}</span>
                            <span class="block text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</span>
                        </div>
                    </div>

                    <!-- Botón de Salir -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Cerrar sesión" class="p-2 text-slate-400 hover:text-red-400 transition">
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Área de Contenido Principal (Derecha) -->
        <div class="flex-1 ml-64 flex flex-col justify-between min-h-screen">
            <div>
                <!-- Header Top Bar -->
                <header class="h-20 bg-white border-b border-gray-200 px-8 flex items-center justify-between shadow-sm sticky top-0 z-40">
                    <div>
                        @isset($header)
                        {{ $header }}
                        @else
                        <h1 class="text-xl font-bold text-slate-900">{{ $corp->company_name }}</h1>
                        @endisset
                    </div>

                    <div class="flex items-center space-x-3">
                        @if ($activeRoleObj)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 capitalize">
                                <i class="fa-solid fa-user-check mr-1.5 text-slate-500"></i>
                                Rol: {{ $activeRoleObj->name }}
                            </span>
                        @endif
                        @if (Auth::check() && Auth::user()->hasMultipleRoles())
                            <a href="{{ route('active-role.edit') }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">
                                <i class="fa-solid fa-arrows-rotate mr-1.5"></i> Cambiar rol
                            </a>
                        @endif
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                            <i class="fa-solid fa-circle text-green-500 mr-2 text-[8px]"></i> Sistema Activo
                        </span>
                    </div>
                </header>

                <!-- Contenido Principal -->
                <main class="p-8 space-y-4">
                    @if (session('success'))
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center justify-between">
                                <span><i class="fa-solid fa-circle-check mr-2 text-emerald-600"></i>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800 flex items-center justify-between">
                                <span><i class="fa-solid fa-triangle-exclamation mr-2 text-red-600"></i>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>

            <!-- Footer General -->
            <footer class="bg-white border-t border-gray-200 py-4 px-8 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ $corp->company_name }} ({{ $corp->business_subtitle }}) · NIT {{ $corp->nit }} | Software de Gestión Empresarial (SGE).
            </footer>
        </div>

    </div>

    @stack('scripts')
</body>

</html>
