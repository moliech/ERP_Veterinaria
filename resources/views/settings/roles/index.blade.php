<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-800 leading-tight">Configuración — Roles</h2>
            <p class="text-xs text-slate-500 mt-0.5">Administración de roles de acceso y agrupación de permisos por puesto de trabajo</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl p-6 space-y-5 font-sans">

                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <a href="{{ route('settings.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-xs font-semibold transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i> Volver a Configuración
                    </a>
                    @can('crear-roles')
                        <a href="{{ route('settings.roles.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-plus"></i> Nuevo rol
                        </a>
                    @endcan
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                            <tr class="text-left">
                                <th class="px-4 py-3">Rol</th>
                                <th class="px-4 py-3">Usuarios</th>
                                <th class="px-4 py-3">Permisos</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($roles as $role)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-800 capitalize">
                                        {{ $role->name }}
                                        @if ($role->name === $superRole)
                                            <span class="ml-2 inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-[11px] font-semibold text-amber-800 normal-case">
                                                Todos los permisos
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-mono text-slate-700">{{ $role->users_count }}</td>
                                    <td class="px-4 py-3 font-mono text-slate-700">{{ $role->permissions_count }}</td>
                                    <td class="px-4 py-3 text-right space-x-3 whitespace-nowrap">
                                        @can('editar-roles')
                                            <a href="{{ route('settings.roles.edit', $role) }}" class="text-blue-700 hover:text-blue-900 font-semibold text-xs">
                                                {{ $role->name === $superRole ? 'Ver detalle' : 'Editar' }}
                                            </a>
                                        @endcan
                                        @can('eliminar-roles')
                                            @if ($role->name !== $superRole)
                                                <form method="POST" action="{{ route('settings.roles.destroy', $role) }}" class="inline"
                                                      onsubmit="return confirm('¿Eliminar el rol {{ $role->name }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs">Eliminar</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
