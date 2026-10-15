<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Clientes y Propietarios') }}
            </h2>

            @can('crear-clientes')
                <a href="{{ route('clients.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm text-sm transition flex items-center space-x-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>+ Nuevo Cliente</span>
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

                <form method="GET" action="{{ route('clients.index') }}"
                      class="flex flex-wrap gap-2 items-center">
                    <div class="relative flex-1 min-w-[280px]">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Buscar por nombre, documento o correo..."
                            class="border-slate-300 rounded-xl w-full text-sm pl-9 pr-4 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        >
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>

                    <button type="submit"
                            class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                        Buscar
                    </button>

                    @if (request('search'))
                        <a href="{{ route('clients.index') }}" class="text-slate-500 hover:text-slate-800 text-sm font-medium px-2">
                            Limpiar
                        </a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-900 text-white text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Documento</th>
                                <th class="px-4 py-3 text-left">Nombre Completo</th>
                                <th class="px-4 py-3 text-left">Correo Electrónico</th>
                                <th class="px-4 py-3 text-center">Ventas</th>
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white text-sm">
                            @forelse ($clients as $client)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-mono text-slate-800">
                                        <span class="font-bold">{{ $client->document_type }}</span> {{ $client->document_number }}
                                    </td>

                                    <td class="px-4 py-3 font-semibold text-slate-900">
                                        {{ $client->first_name }} {{ $client->last_name }}
                                    </td>

                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $client->email ?? 'N/A' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full font-bold">
                                            {{ $client->sales_count }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center space-x-3">
                                        @can('editar-clientes')
                                            <a href="{{ route('clients.edit', $client) }}"
                                               class="text-indigo-600 hover:text-indigo-900 font-bold transition">
                                                Editar
                                            </a>
                                        @endcan

                                        @can('eliminar-clientes')
                                            <form action="{{ route('clients.destroy', $client) }}" method="POST"
                                                  class="inline-block"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 font-bold transition">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                        No hay clientes registrados que coincidan con la búsqueda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>