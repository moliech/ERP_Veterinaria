<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Catálogo e Inventario de Productos') }}
            </h2>
            <a href="{{ route('products.create') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded shadow">
                + Registrar Nuevo Producto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow">
                    <p class="font-bold">¡Operación Exitosa!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-slate-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Categoría</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Precio</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Stock</th>
                            <th class="px-4 py-3 text-center text-xs uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($products as $product)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-700">
                                    {{ $product->barcode ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-slate-800">
                                    {{ $product->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <span class="px-2 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $product->category->name ?? 'Sin Categoría' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-emerald-700">
                                    ${{ number_format($product->sale_price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="{{ $product->current_stock <= $product->minimum_stock ? 'text-red-600 font-bold' : 'text-slate-700' }}">
                                        {{ $product->current_stock }} unidades
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-medium space-x-2">
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="text-indigo-600 hover:text-indigo-900 font-bold">
                                        Editar
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}"
                                          method="POST" class="inline-block"
                                          onsubmit="return confirm('¿Eliminar producto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 font-bold">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    No existen productos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>