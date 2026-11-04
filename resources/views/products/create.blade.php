<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Registrar Nuevo Producto y Lote en Inventario') }}
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                ← Volver a Inventario
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b pb-2">
                        1. Identificación y Clasificación en Bodega
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Nombre del Producto *')" />
                            <x-text-input id="name" name="name" type="text"
                                class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="category_id" :value="__('Categoría *')" />
                            <select id="category_id" name="category_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Seleccione una Categoría --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="barcode" :value="__('Código de Barras / SKU')" />
                            <x-text-input id="barcode" name="barcode" type="text"
                                class="mt-1 block w-full" :value="old('barcode')" placeholder="Ej: 770015" />
                            <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="cost_price" :value="__('Costo Unitario en Bodega ($) *')" />
                            <x-text-input id="cost_price" name="cost_price" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('cost_price', 0)" required />
                            <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
                        </div>
                    </div>

                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b pb-2 pt-2">
                        2. Control de Lote, Fecha de Vencimiento y Existencias
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="batch_code" :value="__('Código de Lote (Para alimentos / insumos perecederos)')" />
                            <x-text-input id="batch_code" name="batch_code" type="text"
                                class="mt-1 block w-full" :value="old('batch_code')" placeholder="Ej: LOT-2026-09A (Dejar vacío si no aplica)" />
                            <x-input-error :messages="$errors->get('batch_code')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="expiration_date" :value="__('Fecha de Vencimiento del Lote')" />
                            <x-text-input id="expiration_date" name="expiration_date" type="date"
                                class="mt-1 block w-full" :value="old('expiration_date')" />
                            <span class="text-[11px] text-slate-400">Dejar vacío para accesorios o artículos no perecederos.</span>
                            <x-input-error :messages="$errors->get('expiration_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="current_stock" :value="__('Stock Inicial en Bodega *')" />
                            <x-text-input id="current_stock" name="current_stock" type="number"
                                class="mt-1 block w-full" :value="old('current_stock', 0)" required />
                            <x-input-error :messages="$errors->get('current_stock')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="minimum_stock" :value="__('Stock Mínimo (Alerta Reabastecimiento) *')" />
                            <x-text-input id="minimum_stock" name="minimum_stock" type="number"
                                class="mt-1 block w-full" :value="old('minimum_stock', 5)" required />
                            <x-input-error :messages="$errors->get('minimum_stock')" class="mt-2" />
                        </div>
                    </div>

                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b pb-2 pt-2">
                        3. Tarifa Comercial Inicial (Administrable en Módulo de Tarifas)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div>
                            <x-input-label for="sale_price" :value="__('Precio de Venta Base sin IVA ($) *')" />
                            <x-text-input id="sale_price" name="sale_price" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('sale_price')" required />
                            <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tax_rate" :value="__('Tarifa de IVA Diferencial')" />
                            <select id="tax_rate" name="tax_rate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @foreach (\App\Models\Product::availableTaxRates() as $rate => $label)
                                    <option value="{{ $rate }}" @selected(old('tax_rate', '19.00') == $rate)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('tax_rate')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-2 border-t">
                        <a href="{{ route('products.index') }}" class="text-gray-600 text-sm font-semibold">Cancelar</a>
                        <x-primary-button class="bg-emerald-600">
                            {{ __('Guardar en Inventario') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
