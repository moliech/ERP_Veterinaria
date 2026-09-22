<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Producto: ') }} {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Nombre del Producto *')" />
                            <x-text-input id="name" name="name" type="text"
                                class="mt-1 block w-full" :value="old('name', $product->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="category_id" :value="__('Categoría *')" />
                            <select id="category_id" name="category_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="barcode" :value="__('Código de Barras')" />
                            <x-text-input id="barcode" name="barcode" type="text"
                                class="mt-1 block w-full" :value="old('barcode', $product->barcode)" />
                            <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="sale_price" :value="__('Precio de Venta ($) *')" />
                            <x-text-input id="sale_price" name="sale_price" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('sale_price', $product->sale_price)" required />
                            <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="current_stock" :value="__('Stock Actual *')" />
                            <x-text-input id="current_stock" name="current_stock" type="number"
                                class="mt-1 block w-full" :value="old('current_stock', $product->current_stock)" required />
                            <x-input-error :messages="$errors->get('current_stock')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="minimum_stock" :value="__('Stock Mínimo *')" />
                            <x-text-input id="minimum_stock" name="minimum_stock" type="number"
                                class="mt-1 block w-full" :value="old('minimum_stock', $product->minimum_stock)" required />
                            <x-input-error :messages="$errors->get('minimum_stock')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('products.index') }}" class="text-gray-600">Cancelar</a>
                        <x-primary-button class="bg-indigo-600">
                            {{ __('Actualizar Producto') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>