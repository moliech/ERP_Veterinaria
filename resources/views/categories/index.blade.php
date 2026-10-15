<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Categorías de Productos') }} {{ $showTrashed ? '(Papelera)' : '' }}
            </h2>

            @can('crear-categorias')
                <a href="{{ route('categories.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm text-sm transition flex items-center space-x-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>+ Nueva Categoría</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">

                @if (session('success'))
                    <div class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded-xl shadow-sm flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-800 rounded-xl shadow-sm flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-red-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-xs uppercase font-bold tracking-wider text-slate-500">
                        Catálogo de Clasificación de Productos e Insumos
                    </span>

                    @can('eliminar-categorias')
                        @if ($showTrashed)
                            <a href="{{ route('categories.index') }}" class="text-sm text-emerald-600 font-bold hover:underline flex items-center space-x-1">
                                <i class="fa-solid fa-list-check text-xs"></i>
                                <span>Ver Categorías Activas</span>
                            </a>
                        @else
                            <a href="{{ route('categories.index', ['trashed' => 1]) }}" class="text-sm text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                                <span>Ver Papelera (Papelera SoftDelete)</span>
                            </a>
                        @endif
                    @endcan
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-900 text-white text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Nombre de la Categoría</th>
                                <th class="px-4 py-3 text-left">Descripción</th>
                                <th class="px-4 py-3 text-center">Productos Asociados</th>
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white text-sm">
                            @forelse ($categories as $category)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-bold text-slate-900">
                                        {{ $category->name }}
                                    </td>

                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $category->description ?? 'Sin descripción' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full font-bold">
                                            {{ $category->products_count ?? $category->products()->count() }} productos
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center space-x-3">
                                        @if ($category->trashed())
                                            <form action="{{ route('categories.restore', $category) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:text-green-900 font-bold hover:underline">
                                                    Restaurar
                                                </button>
                                            </form>
                                        @else
                                            @can('editar-categorias')
                                                <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 font-bold transition">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('eliminar-categorias')
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline-block"
                                                      onsubmit="return confirm('¿Seguro que deseas eliminar la categoría «{{ $category->name }}»?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold transition">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                        No hay categorías registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
