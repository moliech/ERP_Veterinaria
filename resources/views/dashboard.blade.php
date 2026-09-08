<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            Panel de Control (Dashboard)
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Mensaje de Bienvenida -->
        <div class="bg-gradient-to-r from-emerald-700 to-teal-800 rounded-2xl shadow-lg p-6 text-white flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold">¡Bienvenido de nuevo, {{ Auth::user()->name }}!</h1>
                <p class="text-emerald-100 text-sm mt-1">Resumen general de las operaciones de la clínica veterinaria y petshop hoy.</p>
            </div>
            <a href="#" class="px-4 py-2.5 bg-white text-emerald-800 font-bold rounded-xl text-sm shadow hover:bg-emerald-50 transition whitespace-nowrap">
                <i class="fa-solid fa-plus mr-2"></i> Nueva Consulta / Venta
            </a>
        </div>

        <!-- Grid de 4 Indicadores KPIs (Requerimiento Clase 4) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- KPI 1: Pacientes -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pacientes Activos</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 mt-1">128</h3>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-dog"></i>
                    </div>
                </div>
                <span class="block text-xs text-emerald-600 font-medium mt-3">
                    <i class="fa-solid fa-arrow-up mr-1"></i> +12 este mes
                </span>
            </div>

            <!-- KPI 2: Propietarios -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Propietarios</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 mt-1">85</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <span class="block text-xs text-blue-600 font-medium mt-3">
                    <i class="fa-solid fa-user-check mr-1"></i> 100% verificados
                </span>
            </div>

            <!-- KPI 3: Ventas del Día -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ventas del Día</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 mt-1">$1,450.00</h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                </div>
                <span class="block text-xs text-amber-600 font-medium mt-3">
                    <i class="fa-solid fa-cart-shopping mr-1"></i> 14 transacciones
                </span>
            </div>

            <!-- KPI 4: Bajo Stock -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Bajo Stock</span>
                        <h3 class="text-3xl font-extrabold text-rose-600 mt-1">3</h3>
                    </div>
                    <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
                <span class="block text-xs text-rose-600 font-medium mt-3">
                    <i class="fa-solid fa-box-open mr-1"></i> Medicamentos críticos
                </span>
            </div>

        </div>

        <!-- Accesos Rápidos de Administración -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-4">Accesos Rápidos a Módulos</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <a href="#" class="p-4 rounded-xl border border-gray-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                    <i class="fa-solid fa-paw text-2xl text-slate-600 group-hover:text-emerald-600 mb-2"></i>
                    <span class="block text-xs font-semibold text-slate-800">Registrar Mascota</span>
                </a>
                <a href="#" class="p-4 rounded-xl border border-gray-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                    <i class="fa-solid fa-user-plus text-2xl text-slate-600 group-hover:text-emerald-600 mb-2"></i>
                    <span class="block text-xs font-semibold text-slate-800">Nuevo Cliente</span>
                </a>
                <a href="#" class="p-4 rounded-xl border border-gray-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                    <i class="fa-solid fa-pills text-2xl text-slate-600 group-hover:text-emerald-600 mb-2"></i>
                    <span class="block text-xs font-semibold text-slate-800">Catalogo Medicamentos</span>
                </a>
                <a href="#" class="p-4 rounded-xl border border-gray-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-center transition group">
                    <i class="fa-solid fa-file-invoice-dollar text-2xl text-slate-600 group-hover:text-emerald-600 mb-2"></i>
                    <span class="block text-xs font-semibold text-slate-800">Nueva Factura</span>
                </a>
            </div>
        </div>

    </div>
</x-app-layout>