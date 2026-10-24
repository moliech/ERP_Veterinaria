<x-app-layout> 
    <x-slot name="header"> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight"> 
            Dashboard — Bienvenido, {{ auth()->user()->name }} 
        </h2> 
    </x-slot> 
 
    <div class="py-6"> 
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6"> 
 
            {{-- Roles --}} 
            <div class="bg-white shadow rounded p-4"> 
                <p class="text-sm text-gray-600"> 
                    <strong>Roles asignados:</strong> 
                    {{ $roles->isEmpty() ? 'Sin rol asignado' : $roles->join(', ') }} 
                    @if ($roles->count() > 1) 
                        · <strong>Rol activo:</strong> {{ $activeRole }} 
                        (<a href="{{ route('active-role.edit') }}" class="text-blue-700 underline">cambiar</a>) 
                    @endif 
                </p> 
            </div> 
 
            {{-- Accesos rápidos (cada botón aparece solo si el usuario tiene el permiso) --}} 
            <div class="flex flex-wrap gap-2"> 
                @can('crear-productos') 
                    <a href="{{ route('products.create') }}" class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">+ Nuevo producto</a> 
                @endcan 
                @can('crear-categorias') 
                    <a href="{{ route('categories.create') }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">+ Nueva categoría</a> 
                @endcan 
                @can('crear-ventas') 
                    <a href="{{ route('sales.create') }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">+ Nueva venta</a> 
                @endcan 
                @can('crear-compras') 
                    <a href="{{ route('purchases.create') }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">+ Nueva orden de compra</a> 
                @endcan 
            </div> 
 
            {{-- KPIs --}} 
            @if ($stats->isNotEmpty()) 
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"> 
                    @foreach ($stats as $stat) 
                        <div class="bg-white shadow rounded p-4 border-l-4 border-emerald-600"> 
                            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p> 
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ $stat['value'] }}</p> 
                        </div> 
                    @endforeach 
                </div> 
            @endif 
 
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"> 
                {{-- Top productos --}} 
                @if ($topProducts->isNotEmpty()) 
                    <div class="bg-white shadow rounded p-6"> 
                        <h3 class="font-semibold text-lg mb-4">Top 5 productos más vendidos este mes</h3> 
                        <table class="min-w-full text-sm"> 
                            <thead> 
                                <tr class="border-b text-left"> 
                                    <th class="p-2">#</th> 
                                    <th class="p-2">Producto</th> 
                                    <th class="p-2 text-right">Unidades vendidas</th> 
                                </tr> 
                            </thead> 
                            <tbody> 
                                @foreach ($topProducts as $i => $item) 
                                    <tr class="border-b"> 
                                        <td class="p-2">{{ $i + 1 }}</td> 
                                        <td class="p-2">{{ $item->product?->name }}</td> 
                                        <td class="p-2 text-right">{{ $item->total_sold }}</td> 
                                    </tr> 
                                @endforeach 
                            </tbody> 
                        </table> 
                    </div> 
                @endif 
 
                {{-- Sugerencia de reabastecimiento (Clase 10) --}} 
                @if ($lowStock->isNotEmpty()) 
                    <div class="bg-white shadow rounded p-6 border-t-4 border-amber-500"> 
                        <h3 class="font-semibold text-lg mb-4">Productos para reabastecer (stock ≤ 5)</h3> 
                        <table class="min-w-full text-sm"> 
                            <thead> 
                                <tr class="border-b text-left"> 
                                    <th class="p-2">Producto</th> 
                                    <th class="p-2 text-right">Stock actual</th> 
                                </tr> 
                            </thead> 
                            <tbody> 
                                @foreach ($lowStock as $product) 
                                    <tr class="border-b"> 
                                        <td class="p-2">{{ $product->name }}</td> 
                                        <td class="p-2 text-right {{ $product->stock === 0 ? 'text-red-700 font-bold' : '' }}">{{ $product->stock }}</td> 
                                    </tr> 
                                @endforeach 
                            </tbody> 
                        </table> 
                        <a href="{{ route('purchases.create') }}" class="inline-block mt-4 text-blue-700 underline">+ Crear orden de compra</a> 
                    </div> 
                @endif 
            </div> 
 
            {{-- Módulos --}} 
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"> 
                @forelse ($modules as $module) 
                    <a href="{{ route($module['route']) }}" 
                       class="bg-white shadow rounded p-6 hover:shadow-lg transition"> 
                        <h3 class="font-semibold text-lg text-emerald-700">{{ $module['title'] }}</h3> 
                        <p class="text-sm text-gray-600 mt-2">{{ $module['description'] }}</p> 
                    </a> 
                @empty 
                    <div class="col-span-full rounded-md border border-dashed border-gray-300 bg-white p-8 text-center"> 
                        <p class="font-medium text-gray-900">No tienes módulos disponibles.</p> 
                        <p class="mt-1 text-sm text-gray-600">Pide a un administrador que te asigne un rol.</p> 
                    </div> 
                @endforelse 
            </div> 
 
        </div> 
    </div> 
</x-app-layout>
