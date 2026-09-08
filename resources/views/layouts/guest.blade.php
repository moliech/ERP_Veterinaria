<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VetPets ERP') }}</title>

        <!-- Tipografia Figtree -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Font Awesome Iconos -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts & Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-emerald-800 via-teal-900 to-slate-900 min-h-screen text-gray-800">
        <div class="min-h-screen flex flex-col justify-center items-center py-8 px-4 sm:px-6 lg:px-8">
            
            <!-- Branding Header VetPets -->
            <div class="mb-6 text-center">
                <a href="/" class="inline-flex items-center space-x-3 group">
                    <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-emerald-400 transition">
                        <i class="fa-solid fa-paw text-white text-3xl"></i>
                    </div>
                    <div class="text-left">
                        <span class="block text-2xl font-bold text-white tracking-wider">VetPets <span class="text-emerald-400">ERP</span></span>
                        <span class="block text-xs text-emerald-200 uppercase tracking-widest">Gestión Veterinaria & PetShop</span>
                    </div>
                </a>
            </div>

            <!-- Card Contenedor Principal -->
            <div class="w-full sm:max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-emerald-100">
                <div class="p-6 sm:p-8">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer Institucional -->
            <div class="mt-8 text-center text-xs text-emerald-200">
                &copy; {{ date('Y') }} VetPets ERP. Sistema de Gestión Empresarial Veterinario v1.0
            </div>
        </div>
    </body>
</html>