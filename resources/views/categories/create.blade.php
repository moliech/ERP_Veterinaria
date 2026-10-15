<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Registrar Nueva Categoría') }}
            </h2>
            <a href="{{ route('categories.index') }}" class="text-slate-600 hover:text-slate-900 text-sm font-medium flex items-center space-x-1">
                <i class="fa-solid fa-arrow-left"></i> <span>Volver a Categorías</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 md:p-8 shadow-sm rounded-2xl border border-slate-200">

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-800 rounded-xl shadow-sm">
                        <div class="flex items-center space-x-2 font-bold mb-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>Corrige los siguientes errores:</span>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Nombre de la Categoría <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               placeholder="Ej: Farmacia Veterinaria, Alimentos Concentrados, Insumos Quirúrgicos"
                               class="w-full border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm" required>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Descripción (Opcional)
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  placeholder="Detalles sobre los tipos de productos pertenecientes a esta categoría..."
                                  class="w-full border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <a href="{{ route('categories.index') }}" class="text-slate-600 hover:text-slate-900 text-sm font-semibold">
                            Cancelar
                        </a>

                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition flex items-center space-x-2">
                            <i class="fa-solid fa-check"></i>
                            <span>Guardar Categoría</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
