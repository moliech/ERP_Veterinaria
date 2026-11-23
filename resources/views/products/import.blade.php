<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <i class="fa-solid fa-file-excel text-emerald-600"></i> Importación Masiva de Productos
                </h2>
                <p class="text-sm text-slate-500 mt-1">Cargue el catálogo en formato Excel o CSV. Los saldos iniciales se asentarán automáticamente en el Kardex.</p>
            </div>
            <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Volver al Catálogo
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if (session('failures'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-xs">
                <div class="flex items-center gap-2 text-red-800 font-bold mb-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Se encontraron errores en el archivo cargado:
                </div>
                <div class="max-h-48 overflow-y-auto text-xs text-red-700 space-y-1">
                    @foreach (session('failures') as $failure)
                        @php
                            $row = is_array($failure) ? ($failure['row'] ?? '?') : $failure->row();
                            $attr = is_array($failure) ? ($failure['attribute'] ?? '') : $failure->attribute();
                            $errs = is_array($failure) ? ($failure['errors'] ?? []) : $failure->errors();
                        @endphp
                        <p><strong>Fila {{ $row }}:</strong> Campo <code>{{ $attr }}</code> — {{ implode(', ', (array) $errs) }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
            <div class="flex items-start justify-between bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-emerald-900 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-emerald-600"></i> Descargar Plantilla Oficial
                    </h4>
                    <p class="text-xs text-emerald-800">Utilice la plantilla prediseñada para garantizar que las columnas coincidan con el formato requerido.</p>
                </div>
                <a href="{{ route('products.template') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-download"></i> Descargar .xlsx
                </a>
            </div>

            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Seleccionar archivo (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-slate-300 rounded-xl p-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-semibold hover:bg-rose-100 transition">Cancelar</a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Procesar Importación
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>