<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Clientes y Propietarios
            </h2>

            <a href="{{ route('clients.create') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl">
                + Nuevo Cliente
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-xl">

                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('clients.index') }}"
                      class="mb-4 flex gap-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Buscar por nombre, documento o correo"
                        class="border-gray-300 rounded-lg w-full"
                    >

                    <button type="submit"
                            class="bg-slate-900 text-white px-4 py-2 rounded-lg">
                        Buscar
                    </button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-slate-900 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left">Documento</th>
                                <th class="px-4 py-3 text-left">Nombre</th>
                                <th class="px-4 py-3 text-left">Correo</th>
                                <th class="px-4 py-3 text-left">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse ($clients as $client)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $client->document_type }}
                                        {{ $client->document_number }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $client->first_name }}
                                        {{ $client->last_name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $client->email ?? 'N/A' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <a href="{{ route('clients.edit', $client) }}"
                                           class="text-indigo-600 font-bold">
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center">
                                        No hay clientes registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>