<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>VetPets ERP - Sistema de Gestión Empresarial Veterinario</title>

        <!-- Tipografía Figtree -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Font Awesome Iconos -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts & Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between">

        <!-- Navegación Superior -->
        <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                
                <!-- Logo VetPets -->
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-paw text-white text-2xl"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold text-white tracking-wider">VetPets <span class="text-emerald-400">ERP</span></span>
                        <span class="block text-xs text-emerald-300 uppercase tracking-widest">Gestión Veterinaria</span>
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
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-6">
                        <i class="fa-solid fa-shield-cat mr-2"></i> Software de Gestión Empresarial v1.0
                    </span>

                    <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-tight max-w-4xl mx-auto">
                        La solución integral para tu <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200">Clínica Veterinaria & PetShop</span>
                    </h1>

                    <p class="mt-6 text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto">
                        Optimiza la atención de pacientes, control de propietarios, inventario de medicamentos y facturación en una plataforma centralizada y segura.
                    </p>

                    <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                        <a href="{{ route('register') }}" 
                           class="w-full sm:w-auto px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-bold text-lg rounded-xl shadow-xl hover:shadow-emerald-500/20 transition">
                            <i class="fa-solid fa-rocket mr-2"></i> Comenzar gratis
                        </a>
                        <a href="{{ route('login') }}" 
                           class="w-full sm:w-auto px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-lg rounded-xl border border-slate-700 transition">
                            <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> Acceso a Clientes
                        </a>
                    </div>
                </div>
            </section>

            <!-- Módulos Core del ERP -->
            <section class="py-16 bg-slate-900 border-t border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-white">Módulos Principales del Sistema</h2>
                        <p class="text-slate-400 mt-2">Todo lo necesario para administrar tu empresa en un solo lugar</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <!-- Módulo 1 -->
                        <div class="bg-slate-800/60 p-6 rounded-2xl border border-slate-700 hover:border-emerald-500/50 transition">
                            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center text-2xl mb-4">
                                <i class="fa-solid fa-dog"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Pacientes & Mascotas</h3>
                            <p class="text-slate-400 text-sm">Historial clínico unificado, razas, vacunas y seguimiento médico de cada mascota.</p>
                        </div>

                        <!-- Módulo 2 -->
                        <div class="bg-slate-800/60 p-6 rounded-2xl border border-slate-700 hover:border-emerald-500/50 transition">
                            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center text-2xl mb-4">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Clientes & Dueños</h3>
                            <p class="text-slate-400 text-sm">Gestión de propietarios, datos de contacto, historial de visitas y compras asociadas.</p>
                        </div>

                        <!-- Módulo 3 -->
                        <div class="bg-slate-800/60 p-6 rounded-2xl border border-slate-700 hover:border-emerald-500/50 transition">
                            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center text-2xl mb-4">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Inventario & Insumos</h3>
                            <p class="text-slate-400 text-sm">Control de existencias de medicamentos, alertas de bajo stock y fechas de vencimiento.</p>
                        </div>

                        <!-- Módulo 4 -->
                        <div class="bg-slate-800/60 p-6 rounded-2xl border border-slate-700 hover:border-emerald-500/50 transition">
                            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center text-2xl mb-4">
                                <i class="fa-solid fa-cash-register"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Ventas & POS</h3>
                            <p class="text-slate-400 text-sm">Facturación rápida en mostrador, registro de consultas y balance diario de caja.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-950 py-8 border-t border-slate-800 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4">
                <p>&copy; {{ date('Y') }} VetPets ERP. Todos los derechos reservados. Asignatura Software de Gestión Empresarial.</p>
            </div>
        </footer>
    </body>
</html>