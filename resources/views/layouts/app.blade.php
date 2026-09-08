<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VetPets ERP') }}</title>

        <!-- Tipografía Figtree -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Font Awesome Iconos -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts & Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-800 min-h-screen">
        <div class="flex min-h-screen">
            
            <!-- Sidebar Lateral Izquierdo -->
            <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between fixed inset-y-0 left-0 z-50 shadow-xl">
                <div>
                    <!-- Logo Header Sidebar -->
                    <div class="h-20 flex items-center px-6 border-b border-slate-800 bg-slate-950">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fa-solid fa-paw text-white text-xl"></i>
                            </div>
                            <div>
                                <span class="text-lg font-extrabold text-white tracking-wide block leading-none">VetPets <span class="text-emerald-400">ERP</span></span>
                                <span class="text-[10px] text-emerald-300 uppercase tracking-widest font-semibold block mt-1">Gestión Veterinaria</span>
                            </div>
                        </a>
                    </div>

                    <!-- Menú de Módulos Categorizados -->
                    <nav class="mt-6 px-4 space-y-6">
                        
                        <!-- Categoría 1: General -->
                        <div>
                            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">General</span>
                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-chart-line w-6 text-center mr-2 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-emerald-400' }}"></i>
                                Dashboard
                            </a>
                        </div>

                        <!-- Categoría 2: Operaciones -->
                        <div>
                            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Operaciones</span>
                            <div class="space-y-1">
                                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium text-slate-300 rounded-xl hover:bg-slate-800 hover:text-white transition">
                                    <i class="fa-solid fa-dog w-6 text-center mr-2 text-emerald-400"></i>
                                    Pacientes
                                </a>
                                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium text-slate-300 rounded-xl hover:bg-slate-800 hover:text-white transition">
                                    <i class="fa-solid fa-users w-6 text-center mr-2 text-emerald-400"></i>
                                    Clientes
                                </a>
                            </div>
                        </div>

                        <!-- Categoría 3: Inventario & Ventas -->
                        <div>
                            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Inventario & POS</span>
                            <div class="space-y-1">
                                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium text-slate-300 rounded-xl hover:bg-slate-800 hover:text-white transition">
                                    <i class="fa-solid fa-boxes-stacked w-6 text-center mr-2 text-emerald-400"></i>
                                    Productos
                                </a>
                                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium text-slate-300 rounded-xl hover:bg-slate-800 hover:text-white transition">
                                    <i class="fa-solid fa-cash-register w-6 text-center mr-2 text-emerald-400"></i>
                                    Ventas
                                </a>
                            </div>
                        </div>

                    </nav>
                </div>

                <!-- Footer Usuario en Sidebar -->
                <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <div class="w-9 h-9 bg-emerald-600 text-white rounded-lg flex items-center justify-center font-bold text-xs shrink-0">
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
                                <h1 class="text-xl font-bold text-slate-900">VetPets ERP</h1>
                            @endisset
                        </div>

                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="fa-solid fa-circle text-emerald-500 mr-2 text-[8px]"></i> Sistema Activo
                            </span>
                        </div>
                    </header>

                    <!-- Contenido del Dashboard -->
                    <main class="p-8">
                        {{ $slot }}
                    </main>
                </div>

                <!-- Footer General -->
                <footer class="bg-white border-t border-gray-200 py-4 px-8 text-center text-xs text-slate-500">
                    &copy; {{ date('Y') }} VetPets ERP. Sistema de Gestión Empresarial Veterinario v1.0 | Asignatura SGE.
                </footer>
            </div>

        </div>
    </body>
</html>