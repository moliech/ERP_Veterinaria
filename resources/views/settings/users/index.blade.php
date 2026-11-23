<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración — Usuarios</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl p-6 space-y-5 font-sans">

                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <a href="{{ route('settings.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-xs font-semibold transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i> Volver a Configuración
                    </a>
                    @can('crear-usuarios')
                        <a href="{{ route('settings.users.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-user-plus"></i> Nuevo usuario
                        </a>
                    @endcan
                </div>

                <form method="GET" action="{{ route('settings.users.index') }}" class="flex flex-wrap items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o correo..." class="border-slate-300 rounded-xl text-sm px-3.5 py-2 w-60">
                    <select name="role" class="border-slate-300 rounded-xl text-sm px-3 py-2">
                        <option value="">Todos los roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="border-slate-300 rounded-xl text-sm px-3 py-2">
                        <option value="">Todos los estados</option>
                        <option value="active" @selected(request('status') === 'active')>Activos</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition">Filtrar</button>
                    @if (request()->anyFilled(['search', 'role', 'status']))
                        <a href="{{ route('settings.users.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-semibold transition">Limpiar</a>
                    @endif
                </form>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr class="text-left">
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Correo</th>
                            <th class="px-4 py-3">Roles</th>
                            <th class="px-4 py-3">Permisos directos</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 space-x-1">
                                    @forelse ($user->roles as $role)
                                        <span class="inline-block rounded-full bg-blue-50 border border-blue-200 px-2.5 py-0.5 text-xs font-semibold text-blue-800">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-xs text-slate-400">Sin rol</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-700">{{ $user->permissions_count }}</td>
                                <td class="px-4 py-3">
                                    <x-status-badge :active="$user->active" />
                                </td>
                                <td class="px-4 py-3 space-x-2 whitespace-nowrap">
                                    @can('editar-usuarios')
                                        <a href="{{ route('settings.users.edit', $user) }}" class="text-blue-700 hover:text-blue-900 font-semibold text-xs">Editar</a>
                                        @unless ($user->is(auth()->user()))
                                            <form method="POST" action="{{ route('settings.users.toggle', $user) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="{{ $user->active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }} font-semibold text-xs">
                                                    {{ $user->active ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        @endunless
                                    @endcan
                                    <a href="{{ route('settings.permissions.index', ['user' => $user->id]) }}" class="text-slate-600 hover:text-slate-900 font-semibold text-xs">Ver permisos</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">No hay usuarios para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
