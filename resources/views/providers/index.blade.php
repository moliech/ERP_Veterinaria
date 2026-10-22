<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Gestión de Proveedores y Laboratorios') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Catálogo de abastecimiento, medicamentos, alimentos e insumos veterinarios</p>
            </div>

            @can('crear-proveedores')
                <a href="{{ route('providers.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm text-sm transition flex items-center space-x-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>+ Nuevo Proveedor</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">

                @if (session('success'))
                    <div class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded-xl shadow-sm flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-800 rounded-xl shadow-sm flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-red-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Buscador -->
                <form method="GET" action="{{ route('providers.index') }}" class="flex items-center space-x-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Buscar por razón social, NIT o persona de contacto..."
                               class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                        Buscar
                    </button>
                    @if(request('search'))
                        <a href="{{ route('providers.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">
                            Limpiar
                        </a>
                    @endif
                </form>

                <!-- Tabla -->
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">NIT</th>
                                <th class="px-4 py-3 text-left">Razón Social / Laboratorio</th>
                                <th class="px-4 py-3 text-left">Contacto</th>
                                <th class="px-4 py-3 text-left">Teléfono & Correo</th>
                                <th class="px-4 py-3 text-center">Órdenes</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($providers as $provider)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 font-mono font-medium text-slate-700 whitespace-nowrap">
                                        {{ $provider->nit }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">
                                        {{ $provider->company_name }}
                                        @if($provider->address)
                                            <div class="text-[11px] text-slate-400 font-normal">{{ $provider->address }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $provider->contact_person ?? 'Sin contacto' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs">
                                        <div><i class="fa-solid fa-phone text-slate-400 mr-1"></i>{{ $provider->phone ?? 'N/D' }}</div>
                                        @if($provider->email)
                                            <div class="text-slate-400"><i class="fa-solid fa-envelope mr-1"></i>{{ $provider->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $provider->purchases_count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $provider->purchases_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                        @can('editar-proveedores')
                                            <a href="{{ route('providers.edit', $provider) }}"
                                               class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                                                <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                            </a>
                                        @endcan

                                        @can('eliminar-proveedores')
                                            <form action="{{ route('providers.destroy', $provider) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar este proveedor?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-900">
                                                    <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                        No se encontraron proveedores registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $providers->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
