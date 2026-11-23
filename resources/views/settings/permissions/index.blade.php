<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-800 leading-tight">Configuración — Matriz de Permisos</h2>
            <p class="text-xs text-slate-500 mt-0.5">Control centralizado de permisos por módulo, rol y verificación de accesos por usuario</p>
        </div>
    </x-slot>

    @php
        $canEdit = auth()->user()->can('editar-roles');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl p-6 space-y-5 font-sans">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <a href="{{ route('settings.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-xs font-semibold transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i> Volver a Configuración
                    </a>
                </div>

                {{-- Filtros --}}
                <form method="GET" action="{{ route('settings.permissions.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="module" value="Módulo (categoría)" />
                        <select id="module" name="module" class="mt-1 border-slate-300 rounded-xl text-sm px-3 py-2">
                            <option value="">Todos los módulos</option>
                            @foreach ($modules as $key => $label)
                                <option value="{{ $key }}" @selected($module === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="user" value="Ver como usuario" />
                        <select id="user" name="user" class="mt-1 border-slate-300 rounded-xl text-sm px-3 py-2">
                            <option value="">— Ninguno —</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected($inspected?->id === $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition">Aplicar</button>
                    @if (request()->anyFilled(['module', 'user']))
                        <a href="{{ route('settings.permissions.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-semibold transition">Limpiar</a>
                    @endif
                </form>

                @if ($inspected)
                    <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm text-blue-900">
                        <strong>{{ $inspected->name }}</strong> — roles:
                        {{ $inspected->roles->pluck('name')->join(', ') ?: 'ninguno' }}.
                        La última columna muestra sus permisos efectivos y de dónde vienen.
                        @if ($inspected->roles->count() > 1)
                            Como tiene varios roles, en cada sesión solo aplica el rol que elija + sus permisos directos.
                        @endif
                    </div>
                @endif

                {{-- Matriz: filas = permisos por módulo, columnas = roles --}}
                <form method="POST" action="{{ route('settings.permissions.matrix') }}">
                    @csrf
                    @method('PUT')

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                                <tr class="text-left">
                                    <th class="px-4 py-3">Permiso</th>
                                    <th class="px-4 py-3">Acción</th>
                                    @foreach ($roles as $role)
                                        <th class="px-4 py-3 text-center capitalize">{{ $role->name }}</th>
                                    @endforeach
                                    <th class="px-4 py-3">Usuarios con permiso directo</th>
                                    @if ($inspected)
                                        <th class="px-4 py-3 bg-blue-50 text-blue-800">{{ $inspected->name }}</th>
                                    @endif
                                    @can('eliminar-permisos')
                                        <th class="px-4 py-3 text-right">Acciones</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($groups as $key => $group)
                                    <tr class="bg-slate-100/80">
                                        <td colspan="{{ 4 + $roles->count() + ($inspected ? 1 : 0) }}" class="px-4 py-2 font-bold text-xs uppercase tracking-wider text-slate-700">
                                            {{ $group['label'] }}
                                        </td>
                                    </tr>
                                    @foreach ($group['permissions'] as $action => $permission)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="px-4 py-2.5 font-mono text-xs text-slate-700">
                                                {{ $permission->name }}
                                                <input type="hidden" name="visible[]" value="{{ $permission->name }}">
                                            </td>
                                            <td class="px-4 py-2.5 text-slate-700">{{ \App\Support\PermissionCatalog::actionLabel($action) }}</td>
                                            @foreach ($roles as $role)
                                                <td class="px-4 py-2.5 text-center">
                                                    <input type="checkbox" name="matrix[{{ $role->id }}][]" value="{{ $permission->name }}"
                                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                                           @checked(in_array($permission->name, $rolePermissions[$role->id]))
                                                           @disabled(! $canEdit || $role->name === $superRole)>
                                                </td>
                                            @endforeach
                                            <td class="px-4 py-2.5 text-xs text-slate-600">
                                                {{ $permission->users->pluck('name')->join(', ') ?: '—' }}
                                            </td>
                                            @if ($inspected)
                                                <td class="px-4 py-2.5 text-xs bg-blue-50/70">
                                                    @if (isset($origins[$permission->name]))
                                                        <span class="text-emerald-700 font-bold">✓</span> {{ implode(' · ', $origins[$permission->name]) }}
                                                    @else
                                                        <span class="text-slate-400">✗</span>
                                                    @endif
                                                </td>
                                            @endif
                                            @can('eliminar-permisos')
                                                <td class="px-4 py-2.5 text-right">
                                                    <button type="submit" form="delete-{{ $permission->id }}" class="text-xs font-semibold text-rose-600 hover:text-rose-800">Eliminar</button>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($canEdit)
                        <div class="mt-4 flex items-center gap-4">
                            <x-primary-button>Guardar matriz</x-primary-button>
                            <span class="text-xs text-gray-500">La columna «{{ $superRole }}» no se edita: siempre tiene todos los permisos.</span>
                        </div>
                    @endif
                </form>

                {{-- Formularios de eliminación (fuera del formulario de la matriz: HTML no permite formularios anidados) --}}
                @can('eliminar-permisos')
                    @foreach ($groups as $group)
                        @foreach ($group['permissions'] as $permission)
                            <form id="delete-{{ $permission->id }}" method="POST" action="{{ route('settings.permissions.destroy', $permission) }}"
                                  onsubmit="return confirm('¿Eliminar el permiso {{ $permission->name }}?')">
                                @csrf @method('DELETE')
                            </form>
                        @endforeach
                    @endforeach
                @endcan
            </div>

            {{-- Crear permiso --}}
            @can('crear-permisos')
                <div class="bg-white shadow-sm border border-slate-200 rounded-2xl p-6 space-y-4 font-sans">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">Nuevo permiso</h3>
                        <p class="text-sm text-slate-600 mt-1">
                            Registre una nueva acción sobre un módulo del sistema (por ejemplo: <strong>aprobar</strong> en <strong>compras</strong>).
                            El permiso se asignará automáticamente al rol <strong>{{ ucfirst($superRole) }}</strong> y quedará disponible en la matriz para habilitarlo en otros roles o usuarios.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('settings.permissions.store') }}" class="flex flex-wrap items-end gap-2">
                        @csrf
                        <div>
                            <x-input-label for="action" value="Acción" />
                            <x-text-input id="action" name="action" list="actions-list" class="mt-1" :value="old('action')" placeholder="aprobar" required />
                            <datalist id="actions-list">
                                @foreach (config('permisos.actions') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </datalist>
                        </div>
                        <div>
                            <x-input-label for="new-module" value="Módulo" />
                            <x-text-input id="new-module" name="module" list="modules-list" class="mt-1" :value="old('module')" placeholder="compras" required />
                            <datalist id="modules-list">
                                @foreach ($modules as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </datalist>
                        </div>
                        <x-primary-button>Crear permiso</x-primary-button>
                    </form>
                    <x-input-error :messages="$errors->get('action')" />
                    <x-input-error :messages="$errors->get('module')" />
                    <x-input-error :messages="$errors->get('name')" />
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
