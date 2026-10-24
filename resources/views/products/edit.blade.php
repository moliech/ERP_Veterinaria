<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Inventario y Lote: ') }} {{ $product->name }}
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                ← Volver a Inventario
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b pb-2">
                        1. Identificación y Costo en Bodega
                    </h3>
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
                            <x-input-label for="barcode" :value="__('Código de Barras / SKU')" />
                            <x-text-input id="barcode" name="barcode" type="text"
                                class="mt-1 block w-full" :value="old('barcode', $product->barcode)" />
                            <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="cost_price" :value="__('Costo Unitario en Bodega ($) *')" />
                            <x-text-input id="cost_price" name="cost_price" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('cost_price', $product->cost_price)" required />
                            <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
                        </div>
                    </div>

                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b pb-2 pt-2">
                        2. Control de Lote, Fecha de Vencimiento y Existencias
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="batch_code" :value="__('Código de Lote')" />
                            <x-text-input id="batch_code" name="batch_code" type="text"
                                class="mt-1 block w-full" :value="old('batch_code', $product->batch_code)" placeholder="Ej: LOT-2026-09A" />
                            <x-input-error :messages="$errors->get('batch_code')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="expiration_date" :value="__('Fecha de Vencimiento del Lote')" />
                            <x-text-input id="expiration_date" name="expiration_date" type="date"
                                class="mt-1 block w-full" :value="old('expiration_date', $product->expiration_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('expiration_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="current_stock" :value="__('Stock Actual en Bodega *')" />
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

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div class="text-xs text-slate-600">
                            <strong>Tarifa Comercial Vigente:</strong>
                            Precio Base: <span class="font-mono font-bold">${{ number_format((float) $product->sale_price, 0, ',', '.') }}</span> ·
                            IVA: <span class="font-bold">{{ number_format((float) $product->tax_rate, 0) }}%</span> ·
                            Margen: <span class="font-bold text-emerald-700">{{ $product->profitMargin() }}%</span>
                        </div>
                        <a href="{{ route('pricing.index', ['search' => $product->barcode ?: $product->name]) }}"
                           class="text-xs font-bold text-emerald-600 hover:underline">
                            Modificar en Módulo de Tarifas →
                        </a>
                    </div>

                    <input type="hidden" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
                    <input type="hidden" name="tax_rate" value="{{ old('tax_rate', $product->tax_rate) }}">

                    <div class="flex items-center justify-end gap-4 pt-2 border-t">
                        <a href="{{ route('products.index') }}" class="text-gray-600 text-sm font-semibold">Cancelar</a>
                        <x-primary-button class="bg-emerald-600">
                            {{ __('Actualizar Inventario y Lote') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
