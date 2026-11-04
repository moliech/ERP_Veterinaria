<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración del Sistema</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="bg-white shadow rounded-xl p-4 text-sm text-gray-700 border-l-4 border-emerald-600">
                <strong>Centro de Administración ERP:</strong>
                Gestione el control de acceso basado en roles (RBAC), la <strong>Identidad Corporativa</strong> de la empresa (reflejada en la interfaz y en los documentos PDF) y la <strong>Gestión de Módulos</strong> operativos del sistema.
            </div>

            {{-- Bloque 1: Seguridad y Control de Acceso --}}
            <div class="space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">1. Seguridad, Usuarios y Permisos</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @can('ver-usuarios')
                        <a href="{{ route('settings.users.index') }}" class="bg-white shadow rounded-xl p-6 hover:shadow-lg transition border border-slate-100">
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-lg text-blue-700">Usuarios</h4>
                                <i class="fa-solid fa-user-shield text-blue-600 text-xl"></i>
                            </div>
                            <p class="text-3xl font-bold mt-2 text-slate-800">{{ $usersCount }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $inactiveCount }} inactivos · {{ $multiRoleCount }} con varios roles</p>
                        </a>
                    @endcan
                    @can('ver-roles')
                        <a href="{{ route('settings.roles.index') }}" class="bg-white shadow rounded-xl p-6 hover:shadow-lg transition border border-slate-100">
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-lg text-blue-700">Roles</h4>
                                <i class="fa-solid fa-id-badge text-blue-600 text-xl"></i>
                            </div>
                            <p class="text-3xl font-bold mt-2 text-slate-800">{{ $rolesCount }}</p>
                            <p class="text-sm text-gray-600 mt-1">Perfiles de trabajo y segregación de funciones.</p>
                        </a>
                    @endcan
                    @can('ver-permisos')
                        <a href="{{ route('settings.permissions.index') }}" class="bg-white shadow rounded-xl p-6 hover:shadow-lg transition border border-slate-100">
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-lg text-blue-700">Matriz de permisos</h4>
                                <i class="fa-solid fa-table-cells-lock text-blue-600 text-xl"></i>
                            </div>
                            <p class="text-3xl font-bold mt-2 text-slate-800">{{ $permissionsCount }}</p>
                            <p class="text-sm text-gray-600 mt-1">Permisos por rol, módulo y usuario.</p>
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Bloque 2: Parametrización Empresarial y Arquitectura Modular --}}
            <div class="space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">2. Parametrización Empresarial y Arquitectura</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('settings.company.edit') }}" class="bg-white shadow rounded-xl p-6 hover:shadow-lg transition border border-slate-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-lg text-emerald-700">Identidad Corporativa</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Datos fiscales, logotipo, colores institucionales y pie de factura PDF</p>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fa-solid fa-building-columns text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between text-xs text-slate-600 gap-2">
                            <span><strong>Razón Social:</strong> {{ $company->company_name }}</span>
                            <span><strong>NIT:</strong> {{ $company->nit }}</span>
                            <span><strong>IVA:</strong> {{ number_format((float) $company->tax_rate, 0) }}%</span>
                        </div>
                    </a>

                    <a href="{{ route('settings.modules.index') }}" class="bg-white shadow rounded-xl p-6 hover:shadow-lg transition border border-slate-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-lg text-indigo-700">Gestión de Módulos</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Activación dinámica de módulos del ERP y validación de dependencias</p>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i class="fa-solid fa-cubes-stacked text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                            <span><strong>Módulos activos:</strong> {{ $modulesEnabled }} de {{ $modulesTotal }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-semibold">
                                Conservación de historial garantizada
                            </span>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
