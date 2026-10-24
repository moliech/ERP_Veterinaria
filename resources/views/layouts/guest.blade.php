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
        @endphp

        <title>{{ $corp->company_name }} — ERP</title>

        <!-- Tipografia Figtree -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Font Awesome Iconos -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts & Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --corp-50: {{ $colorMeta['50'] ?? '#ecfdf5' }};
                --corp-100: {{ $colorMeta['100'] ?? '#d1fae5' }};
                --corp-200: {{ $colorMeta['200'] ?? '#a7f3d0' }};
                --corp-400: {{ $colorMeta['400'] ?? '#34d399' }};
                --corp-500: {{ $colorMeta['500'] ?? '#10b981' }};
                --corp-600: {{ $colorMeta['600'] ?? '#059669' }};
                --corp-700: {{ $colorMeta['700'] ?? '#047857' }};
                --corp-800: {{ $colorMeta['800'] ?? '#065f46' }};
            }
            .bg-emerald-500 { background-color: var(--corp-500) !important; }
            .bg-emerald-600 { background-color: var(--corp-600) !important; }
            .hover\:bg-emerald-700:hover { background-color: var(--corp-700) !important; }
            .text-emerald-400 { color: var(--corp-400) !important; }
            .text-emerald-600 { color: var(--corp-600) !important; }
            .hover\:text-emerald-800:hover { color: var(--corp-800) !important; }
            .focus\:border-emerald-500:focus { border-color: var(--corp-500) !important; }
            .focus\:ring-emerald-500:focus { --tw-ring-color: var(--corp-500) !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-900 min-h-screen text-gray-800">
        <div class="min-h-screen flex flex-col justify-center items-center py-8 px-4 sm:px-6 lg:px-8">
            
            <!-- Branding Header Dinámico -->
            <div class="mb-6 text-center">
                <a href="/" class="inline-flex items-center space-x-3 group">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg transition" style="background-color: {{ $accentHex }};">
                        <i class="{{ $corp->icon_class }} text-white text-3xl"></i>
                    </div>
                    <div class="text-left">
                        <span class="block text-2xl font-bold text-white tracking-wider">{{ $corp->company_name }}</span>
                        <span class="block text-xs text-slate-300 uppercase tracking-widest">{{ $corp->business_subtitle }}</span>
                    </div>
                </a>
            </div>

            <!-- Card Contenedor Principal -->
            <div class="w-full sm:max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
                <div class="p-6 sm:p-8">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer Institucional -->
            <div class="mt-8 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} {{ $corp->company_name }} ({{ $corp->business_subtitle }}) · NIT {{ $corp->nit }}
            </div>
        </div>
    </body>
</html>
