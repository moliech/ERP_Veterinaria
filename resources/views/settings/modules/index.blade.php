<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración — Gestión de Módulos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <a href="{{ route('settings.index') }}" class="text-sm text-slate-700 underline">← Volver a Configuración</a>
                <span class="text-xs text-slate-500">Arquitectura modular con control de dependencias e integridad histórica</span>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-900">
                <strong>¿Cómo funciona la Gestión de Módulos?</strong>
                Al deshabilitar un módulo, se oculta automáticamente del menú lateral, del Dashboard y se bloquea el acceso directo a sus rutas mediante el middleware <code>EnsureModuleEnabled</code>, pero <strong>se conserva el 100% de los datos históricos</strong> en la base de datos. El sistema valida automáticamente las dependencias entre módulos antes de permitir desactivarlos.
            </div>

            <div class="bg-white shadow rounded-xl overflow-hidden border border-slate-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 border-b">
                        <tr class="text-left text-slate-700">
                            <th class="p-4">Módulo del ERP</th>
                            <th class="p-4">Dependencias Requeridas</th>
                            <th class="p-4 text-center">Estado Actual</th>
                            <th class="p-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($modules as $module)
                            <tr class="hover:bg-slate-50/80">
                                <td class="p-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-9 h-9 rounded-lg {{ $module->is_enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0 mt-0.5">
                                            <i class="{{ $module->icon }}"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900">{{ $module->name }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">{{ $module->description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if (! empty($module->depends_on))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($module->depends_on as $depKey)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-medium">
                                                    <i class="fa-solid fa-link mr-1 text-[10px] text-slate-400"></i>
                                                    {{ $labelsByKey[$depKey] ?? $depKey }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Módulo independiente</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <x-status-badge :active="$module->is_enabled" />
                                </td>
                                <td class="p-4 text-right">
                                    <form method="POST" action="{{ route('settings.modules.toggle', $module) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $module->is_enabled ? 'bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                                            {{ $module->is_enabled ? 'Deshabilitar' : 'Habilitar módulo' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
