<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar cliente
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-xl">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('clients.update', $client) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="document_type" class="block font-medium mb-1">
                            Tipo de documento
                        </label>
                        <select name="document_type" id="document_type"
                                class="w-full border-gray-300 rounded-lg" required>
                            <option value="CC" @selected(old('document_type', $client->document_type) == 'CC')>Cédula de ciudadanía</option>
                            <option value="NIT" @selected(old('document_type', $client->document_type) == 'NIT')>NIT</option>
                            <option value="CE" @selected(old('document_type', $client->document_type) == 'CE')>Cédula de extranjería</option>
                            <option value="PP" @selected(old('document_type', $client->document_type) == 'PP')>Pasaporte</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="document_number" class="block font-medium mb-1">
                            Número de documento
                        </label>
                        <input type="text" name="document_number" id="document_number"
                               value="{{ old('document_number', $client->document_number) }}"
                               class="w-full border-gray-300 rounded-lg" required>
                    </div>

                    <div class="mb-4">
                        <label for="first_name" class="block font-medium mb-1">
                            Nombres
                        </label>
                        <input type="text" name="first_name" id="first_name"
                               value="{{ old('first_name', $client->first_name) }}"
                               class="w-full border-gray-300 rounded-lg" required>
                    </div>

                    <div class="mb-4">
                        <label for="last_name" class="block font-medium mb-1">
                            Apellidos
                        </label>
                        <input type="text" name="last_name" id="last_name"
                               value="{{ old('last_name', $client->last_name) }}"
                               class="w-full border-gray-300 rounded-lg" required>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block font-medium mb-1">
                            Correo electrónico
                        </label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $client->email) }}"
                               class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div class="mb-4">
                        <label for="address" class="block font-medium mb-1">
                            Dirección
                        </label>
                        <input type="text" name="address" id="address"
                               value="{{ old('address', $client->address) }}"
                               class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('clients.index') }}"
                           class="px-4 py-2 text-gray-700">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                            Actualizar cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>