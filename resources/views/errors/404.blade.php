@php
    $colorMap = [
        'emerald' => '#059669', 'blue' => '#2563eb', 'indigo' => '#4f46e5',
        'teal' => '#0d9488', 'rose' => '#e11d48', 'amber' => '#d97706', 'slate' => '#334155',
    ];
    try {
        $company = \App\Models\CompanySetting::current();
        $rawColor = $company->primary_color ?? 'emerald';
        $primaryHex = str_starts_with($rawColor, '#') ? $rawColor : ($colorMap[$rawColor] ?? '#059669');
    } catch (\Throwable $e) {
        $company = (object) [
            'company_name' => 'VetPets Comercial',
            'business_subtitle' => 'PetShop & Suministros',
            'icon_class' => 'fa-paw',
        ];
        $primaryHex = '#059669';
    }
    $theme = ['primary' => $primaryHex];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página No Encontrada (404) — {{ $company->company_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6 text-slate-800 antialiased">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden">
        <div class="px-8 py-6 text-white flex items-center justify-between" style="background-color: {{ $theme['primary'] ?? '#059669' }};">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                    <i class="fa-solid {{ $company->icon_class ?? 'fa-paw' }}"></i>
                </div>
                <div>
                    <div class="font-bold text-lg leading-tight">{{ $company->company_name }}</div>
                    <div class="text-xs text-white/80">{{ $company->business_subtitle ?? 'Sistema ERP Comercial' }}</div>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-bold tracking-wider uppercase">
                HTTP 404
            </span>
        </div>

        <div class="p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-slate-100 border border-slate-200 text-slate-500 flex items-center justify-center text-3xl">
                <i class="fa-solid fa-compass"></i>
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Recurso No Encontrado</h1>
            <p class="text-sm text-slate-600 leading-relaxed mb-6">
                La ruta o el registro que intentas consultar no existe o ya no se encuentra disponible.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ auth()->check() ? route('dashboard') : url('/login') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-95"
                   style="background-color: {{ $theme['primary'] ?? '#059669' }};">
                    <i class="fa-solid fa-house"></i>
                    {{ auth()->check() ? 'Volver al Dashboard' : 'Ir al Inicio de Sesión' }}
                </a>
                <button onclick="window.history.back()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    Regresar
                </button>
            </div>
        </div>
    </div>
</body>
</html>
