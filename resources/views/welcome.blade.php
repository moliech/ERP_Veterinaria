<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $corp = $company ?? \App\Models\CompanySetting::current();
            $colorMeta = $corp->colorMeta();
            $accentHex = $corp->primaryColorHex();
        @endphp

        <title>{{ $corp->company_name }} — Sistema de Gestión Empresarial</title>

        <!-- Tipografía Figtree -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Font Awesome Iconos -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts & Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --corp-400: {{ $colorMeta['400'] ?? '#34d399' }};
                --corp-500: {{ $colorMeta['500'] ?? '#10b981' }};
                --corp-600: {{ $colorMeta['600'] ?? '#059669' }};
                --corp-700: {{ $colorMeta['700'] ?? '#047857' }};
            }
            .bg-emerald-500 { background-color: var(--corp-500) !important; }
            .bg-emerald-600 { background-color: var(--corp-600) !important; }
            .hover\:bg-emerald-500:hover { background-color: var(--corp-500) !important; }
            .text-emerald-400 { color: var(--corp-400) !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between">

        <!-- Navegación Superior -->
        <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                
                <!-- Logo Dinámico -->
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background-color: {{ $accentHex }};">
                        <i class="{{ $corp->icon_class }} text-white text-2xl"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold text-white tracking-wider">{{ $corp->company_name }}</span>
                        <span class="block text-xs text-slate-300 uppercase tracking-widest">{{ $corp->business_subtitle }}</span>
                    </div>
                </a>

                <!-- Botones de Acción de Autenticación -->
                <nav class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" 
                               class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm rounded-lg shadow transition">
                                <i class="fa-solid fa-gauge mr-2"></i> Ir al Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="text-sm font-semibold text-slate-300 hover:text-white px-3 py-2 transition">
                                <i class="fa-solid fa-right-to-bracket mr-1"></i> Iniciar sesión
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm rounded-lg shadow-md transition">
                                    <i class="fa-solid fa-user-plus mr-2"></i> Registrarse
                                </a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main>
            <section class="relative py-20 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-emerald-400 border border-slate-700 mb-6">
                        <i class="fa-solid fa-cubes-stacked mr-2"></i> Software de Gestión Empresarial (ERP)
                    </span>

                    <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-tight max-w-4xl mx-auto">
                        Plataforma integral para <span class="text-emerald-400">{{ $corp->company_name }}</span>
                    </h1>

                    <p class="mt-6 text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto">
                        Controla abastecimiento con proveedores, órdenes de compra con recepción parcial, inventario en tiempo real, facturación POS con IVA y seguridad RBAC en una arquitectura modular.
                    </p>

                    <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                        <a href="{{ route('login') }}" 
                           class="w-full sm:w-auto px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-lg rounded-xl shadow-xl transition">
                            <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> Ingresar al Sistema
                        </a>
                    </div>
                </div>
            </section>

            <!-- Módulos Core del ERP -->
            <section class="py-16 bg-slate-900 border-t border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-white">Módulos Principales del Sistema</h2>
                        <p class="text-slate-400 mt-2">Cadena de suministro comercial, punto de venta y parametrización corporativa</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <!-- Módulo 1 -->
                        <div class="bg-slate-800/60 p-6 rounded-2xl border border-slate-700 transition">
                            <div class="w-12 h-12 bg-slate-900 text-emerald-400 rounded-xl flex items-center justify-center text-2xl mb-4">
                                <i class="fa-solid fa-cart-flatbed"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Proveedores & Compras</h3>
                            <p class="text-slate-400 text-sm">Órdenes de compra a distribuidores mayoristas con recepción física parcial o total y soporte PDF.</p>
                        </div>

                        <!-- Módulo 2 -->
                        <div class="bg-slate-800/60 p-6 rounded-2xl border border-slate-700 transition">
                            <div class="w-12 h-12 bg-slate-900 text-emerald-400 rounded-xl flex items-center justify-center text-2xl mb-4">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Catálogo e Inventario</h3>
                            <p class="text-slate-400 text-sm">Control de existencias en tiempo real, categorías comerciales, alertas de bajo stock y papelera lógica.</p>
                        </div>

                        <!-- Módulo 3 -->
                        <div class="bg-slate-800/60 p-6 rounded-2xl border border-slate-700 transition">
                            <div class="w-12 h-12 bg-slate-900 text-emerald-400 rounded-xl flex items-center justify-center text-2xl mb-4">
                                <i class="fa-solid fa-cash-register"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Clientes & Ventas POS</h3>
                            <p class="text-slate-400 text-sm">Directorio de clientes, facturación rápida con liquidación de IVA, descuento automático y anulación reversible.</p>
                        </div>

                        <!-- Módulo 4 -->
                        <div class="bg-slate-800/60 p-6 rounded-2xl border border-slate-700 transition">
                            <div class="w-12 h-12 bg-slate-900 text-emerald-400 rounded-xl flex items-center justify-center text-2xl mb-4">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Seguridad & Módulos</h3>
                            <p class="text-slate-400 text-sm">Control RBAC con rol activo en sesión, matriz de permisos, Identidad Corporativa y Gestión de Módulos.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-950 py-8 border-t border-slate-800 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4">
                <p>&copy; {{ date('Y') }} {{ $corp->company_name }} ({{ $corp->business_subtitle }}) · NIT {{ $corp->nit }}. Asignatura Software de Gestión Empresarial.</p>
            </div>
        </footer>
    </body>
</html>
