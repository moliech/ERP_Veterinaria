<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajuste de inventario: {{ $product->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow rounded p-6 space-y-4">
                <div class="border-b pb-4">
                    <p class="text-sm text-gray-600">Stock registrado en sistema:</p>
                    <p class="text-3xl font-extrabold text-indigo-600">{{ $product->stock }} unidades</p>
                </div>

                @if (session('error'))
                    <div class="p-3 rounded bg-amber-50 text-amber-800 border border-amber-200 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('inventory.adjust.store', $product) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="counted_stock" value="Stock contado físicamente en bodega" />
                        <x-text-input id="counted_stock" type="number" name="counted_stock" min="0" value="{{ old('counted_stock', $product->stock) }}" class="w-full mt-1" required />
                        <x-input-error :messages="$errors->get('counted_stock')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="reason" value="Motivo del ajuste físico" />
                        <textarea id="reason" name="reason" rows="3" class="w-full mt-1 border-gray-300 rounded-md shadow-sm text-sm" placeholder="Ej: Conteo físico mensual, merma justificada, rotura de empaque..." required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('inventory.kardex', ['product' => $product->id]) }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Cancelar y volver al Kardex
                        </a>
                        <x-primary-button>Registrar Ajuste en Kardex</x-primary-button>
                    </div>
                </form>
            </div>

            @if ($lastMovements->isNotEmpty())
                <div class="bg-white shadow rounded p-6 space-y-3">
                    <h3 class="font-semibold text-sm text-gray-700 uppercase tracking-wider">Últimos movimientos registrados</h3>
                    <ul class="divide-y text-xs">
                        @foreach ($lastMovements as $m)
                            <li class="py-2 flex justify-between items-center">
                                <div>
                                    <span class="font-medium {{ $m->type === 'entrada' ? 'text-emerald-700' : 'text-rose-700' }}">{{ $m->conceptLabel() }}</span>
                                    <span class="text-gray-400">({{ $m->created_at->format('d/m/Y H:i') }})</span>
                                    <span class="block text-gray-500 italic">{{ $m->notes }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold">{{ $m->type === 'entrada' ? '+' : '-' }}{{ $m->quantity }}</span>
                                    <span class="block text-gray-400">Saldo: {{ $m->balance }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
