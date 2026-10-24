<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración — Identidad Corporativa</h2>
    </x-slot>

    @php
        $colorHexMap = collect($colors)->mapWithKeys(fn ($meta, $key) => [$key => $meta['hex']])->all();
    @endphp

    <div class="py-6"
         x-data="{
            companyName: @js(old('company_name', $company->company_name)),
            businessSubtitle: @js(old('business_subtitle', $company->business_subtitle)),
            nit: @js(old('nit', $company->nit)),
            address: @js(old('address', $company->address)),
            phone: @js(old('phone', $company->phone)),
            primaryColor: @js(old('primary_color', $company->primary_color)),
            iconClass: @js(old('icon_class', $company->icon_class)),
            colorHexMap: @js($colorHexMap),
            get currentHex() {
                return this.colorHexMap[this.primaryColor] || '#059669';
            }
         }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <a href="{{ route('settings.index') }}" class="text-sm text-slate-700 underline">← Volver a Configuración</a>
                <span class="text-xs text-slate-500">Vista previa interactiva · Al guardar se aplica en el menú lateral y en los documentos PDF</span>
            </div>

            {{-- Vista previa interactiva de Identidad Corporativa --}}
            <div class="bg-slate-900 text-white rounded-xl p-5 shadow-md flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md transition-colors duration-200"
                         :style="'background-color: ' + currentHex"
                         style="background-color: {{ $company->primaryColorHex() }};">
                        <i :class="iconClass + ' text-white text-2xl'" class="{{ $company->icon_class }} text-white text-2xl"></i>
                    </div>
                    <div>
                        <span class="text-lg font-extrabold tracking-wide block leading-none" x-text="companyName">{{ $company->company_name }}</span>
                        <span class="text-xs uppercase tracking-widest font-semibold block mt-1 text-slate-300" x-text="businessSubtitle">{{ $company->business_subtitle }}</span>
                    </div>
                </div>
                <div class="text-right text-xs text-slate-300">
                    <div><strong>NIT:</strong> <span x-text="nit">{{ $company->nit }}</span></div>
                    <div><span x-text="address">{{ $company->address }}</span> · <span x-text="phone">{{ $company->phone }}</span></div>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.company.update') }}" class="bg-white shadow rounded-xl p-6 space-y-6">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="p-3 rounded bg-red-100 text-red-800 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h3 class="font-semibold text-gray-800 border-b pb-2">1. Datos Generales y Fiscales de la Empresa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="company_name" value="Nombre o Razón Social" />
                        <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full"
                                      x-model="companyName"
                                      :value="old('company_name', $company->company_name)" required />
                    </div>
                    <div>
                        <x-input-label for="business_subtitle" value="Subtítulo o Giro Comercial" />
                        <x-text-input id="business_subtitle" name="business_subtitle" type="text" class="mt-1 block w-full"
                                      x-model="businessSubtitle"
                                      :value="old('business_subtitle', $company->business_subtitle)" required />
                    </div>
                    <div>
                        <x-input-label for="nit" value="NIT / Identificación Tributaria" />
                        <x-text-input id="nit" name="nit" type="text" class="mt-1 block w-full"
                                      x-model="nit"
                                      :value="old('nit', $company->nit)" required />
                    </div>
                    <div>
                        <x-input-label for="phone" value="Teléfonos de Contacto" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                      x-model="phone"
                                      :value="old('phone', $company->phone)" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Correo Electrónico Corporativo" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                      :value="old('email', $company->email)" />
                    </div>
                    <div>
                        <x-input-label for="address" value="Dirección Comercial" />
                        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                                      x-model="address"
                                      :value="old('address', $company->address)" />
                    </div>
                </div>

                <h3 class="font-semibold text-gray-800 border-b pb-2 pt-2">2. Parámetros Tributarios y Numeración Documental</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <x-input-label for="tax_rate" value="Impuesto IVA (%)" />
                        <x-text-input id="tax_rate" name="tax_rate" type="number" step="0.01" class="mt-1 block w-full"
                                      :value="old('tax_rate', $company->tax_rate)" required />
                    </div>
                    <div>
                        <x-input-label for="currency" value="Moneda Base" />
                        <x-text-input id="currency" name="currency" type="text" class="mt-1 block w-full"
                                      :value="old('currency', $company->currency)" required />
                    </div>
                    <div>
                        <x-input-label for="invoice_prefix" value="Prefijo Facturas POS" />
                        <x-text-input id="invoice_prefix" name="invoice_prefix" type="text" class="mt-1 block w-full"
                                      :value="old('invoice_prefix', $company->invoice_prefix)" required />
                    </div>
                    <div>
                        <x-input-label for="purchase_prefix" value="Prefijo Órdenes Compra" />
                        <x-text-input id="purchase_prefix" name="purchase_prefix" type="text" class="mt-1 block w-full"
                                      :value="old('purchase_prefix', $company->purchase_prefix)" required />
                    </div>
                </div>

                <h3 class="font-semibold text-gray-800 border-b pb-2 pt-2">3. Personalización Visual y Pie de Documentos PDF</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="primary_color" value="Color Institucional" />
                        <select id="primary_color" name="primary_color" x-model="primaryColor" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($colors as $key => $meta)
                                <option value="{{ $key }}" @selected(old('primary_color', $company->primary_color) === $key)>
                                    {{ $meta['label'] }} ({{ $meta['hex'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="icon_class" value="Ícono Institucional del Sistema" />
                        <select id="icon_class" name="icon_class" x-model="iconClass" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($icons as $iconClass => $label)
                                <option value="{{ $iconClass }}" @selected(old('icon_class', $company->icon_class) === $iconClass)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="invoice_footer" value="Leyenda / Pie de Página en Facturas y Órdenes de Compra (PDF)" />
                        <textarea id="invoice_footer" name="invoice_footer" rows="2"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('invoice_footer', $company->invoice_footer) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t">
                    <x-primary-button>Guardar Identidad Corporativa</x-primary-button>
                    <a href="{{ route('settings.index') }}" class="text-sm underline text-slate-600">Cancelar</a>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
