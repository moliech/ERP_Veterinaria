<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Proveedor: ') . $provider->company_name }}
            </h2>
            <a href="{{ route('providers.index') }}"
               class="text-sm font-semibold text-slate-600 hover:text-slate-900 flex items-center space-x-1">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Volver al Catálogo</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200">
                <form method="POST" action="{{ route('providers.update', $provider) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="nit" :value="__('NIT / Identificación Fiscal *')" />
                            <x-text-input id="nit" class="block mt-1 w-full" type="text" name="nit" :value="old('nit', $provider->nit)" required />
                            <x-input-error :messages="$errors->get('nit')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_name" :value="__('Razón Social / Laboratorio *')" />
                            <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name', $provider->company_name)" required />
                            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_person" :value="__('Persona de Contacto / Asesor')" />
                            <x-text-input id="contact_person" class="block mt-1 w-full" type="text" name="contact_person" :value="old('contact_person', $provider->contact_person)" />
                            <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('Teléfono / WhatsApp')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $provider->phone)" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Correo Electrónico')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $provider->email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="address" :value="__('Dirección de Envío / Sede')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $provider->address)" />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('providers.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">
                            Cancelar
                        </a>
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Actualizar Proveedor') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
