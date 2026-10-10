<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Registrar Nueva Venta (Punto de Venta POS)') }}
            </h2>
            <a href="{{ route('sales.index') }}" 
               class="text-slate-600 hover:text-slate-900 font-medium text-sm flex items-center space-x-1">
                <i class="fa-solid fa-arrow-left"></i> <span>Volver al Listado</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto">

            <!-- Alertas de Error del Servidor -->
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-800 p-4 rounded-xl shadow-sm">
                    <div class="flex items-center space-x-2 font-bold mb-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Por favor corrige los siguientes errores:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sales.store') }}" id="sale-form" 
                  class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 space-y-6">
                @csrf

                <!-- Cabecera: Cliente y Fecha -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="client_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                            Cliente / Propietario <span class="text-red-500">*</span>
                        </label>
                        <select id="client_id" name="client_id" 
                                class="w-full border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm" required>
                            <option value="">-- Seleccione un Propietario --</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                    {{ $client->name }} {{ $client->first_surname }} — Doc: {{ $client->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sale_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                            Fecha de Venta <span class="text-red-500">*</span>
                        </label>
                        <input id="sale_date" name="sale_date" type="date" 
                               value="{{ old('sale_date', now()->format('Y-m-d')) }}" 
                               class="w-full border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm" required>
                    </div>
                </div>

                <!-- Detalle de Productos -->
                <div>
                    <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Ítems de la Venta (Productos e Insumos)</span>
                        <span class="text-xs text-slate-400">Descuento automático de stock</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50 text-slate-700">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs uppercase font-bold">Producto</th>
                                    <th class="px-4 py-2.5 text-center text-xs uppercase font-bold w-28">Cantidad</th>
                                    <th class="px-4 py-2.5 text-right text-xs uppercase font-bold w-36">Precio Unit.</th>
                                    <th class="px-4 py-2.5 text-right text-xs uppercase font-bold w-36">Subtotal</th>
                                    <th class="px-2 py-2.5 text-center w-12"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body" class="divide-y divide-slate-100">
                                <!-- Filas dinámicas inyectadas vía JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="button" id="add-item-btn" 
                                class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 font-semibold px-4 py-2 rounded-xl text-sm transition flex items-center space-x-2">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>+ Agregar Producto</span>
                        </button>
                    </div>
                </div>

                <!-- Resumen de Totales e Impuestos (IVA 19%) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Subtotal</span>
                        <p id="subtotal-display" class="text-xl font-bold text-slate-800 mt-1">$0.00</p>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">IVA (19% DIAN)</span>
                        <p id="tax-display" class="text-xl font-bold text-slate-800 mt-1">$0.00</p>
                    </div>

                    <div class="bg-emerald-500 p-4 rounded-xl shadow-sm text-white">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-100 block">Total a Pagar</span>
                        <p id="total-display" class="text-2xl font-black mt-1">$0.00</p>
                    </div>
                </div>

                <!-- Observaciones -->
                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Notas u Observaciones (Opcional)
                    </label>
                    <textarea id="notes" name="notes" rows="2" 
                              placeholder="Ej: Pago con tarjeta de crédito, indicaciones de dosificación..." 
                              class="w-full border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">{{ old('notes') }}</textarea>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-100">
                    <a href="{{ route('sales.index') }}" 
                       class="text-slate-600 hover:text-slate-900 font-semibold text-sm">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Confirmar y Procesar Venta</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Template HTML para filas dinámicas de JavaScript -->
    <template id="item-row-template">
        <tr class="item-row hover:bg-slate-50 transition">
            <td class="px-4 py-3">
                <select name="items[__INDEX__][product_id]" 
                        class="product-select w-full border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm" required>
                    <option value="">-- Seleccionar Producto --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" 
                                data-price="{{ $product->sale_price }}" 
                                data-stock="{{ $product->current_stock }}">
                            {{ $product->name }} (Disponible: {{ $product->current_stock }} uds) - ${{ number_format($product->sale_price, 2) }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-3 text-center">
                <input type="number" name="items[__INDEX__][quantity]" value="1" min="1" 
                       class="quantity-input w-24 text-center border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm" required>
            </td>
            <td class="px-4 py-3 text-right font-medium text-slate-700">
                <span class="price-display">$0.00</span>
            </td>
            <td class="px-4 py-3 text-right font-bold text-slate-900">
                <span class="subtotal-display">$0.00</span>
            </td>
            <td class="px-2 py-3 text-center">
                <button type="button" class="remove-row-btn text-red-500 hover:text-red-700 p-1.5 transition rounded-lg hover:bg-red-50" title="Eliminar fila">
                    <i class="fa-solid fa-trash-can text-sm"></i>
                </button>
            </td>
        </tr>
    </template>

    @push('scripts')
    <script>
        let rowIndex = 0;
        const itemsBody = document.getElementById('items-body');
        const template = document.getElementById('item-row-template').innerHTML;
        const addBtn = document.getElementById('add-item-btn');
        const oldItems = @json(array_values(old('items', [])));

        function formatCOP(val) {
            return '$' + Number(val).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function addRow(itemData = null) {
            const html = template.replace(/__INDEX__/g, rowIndex++);
            itemsBody.insertAdjacentHTML('beforeend', html);
            const row = itemsBody.lastElementChild;

            if (itemData) {
                const select = row.querySelector('.product-select');
                const qtyInput = row.querySelector('.quantity-input');
                select.value = itemData.product_id || '';
                qtyInput.value = itemData.quantity || 1;
            }

            bindRowEvents(row);
            recalculateTotals();
        }

        function bindRowEvents(row) {
            const select = row.querySelector('.product-select');
            const qtyInput = row.querySelector('.quantity-input');
            const removeBtn = row.querySelector('.remove-row-btn');

            select.addEventListener('change', recalculateTotals);
            qtyInput.addEventListener('input', recalculateTotals);
            removeBtn.addEventListener('click', () => {
                row.remove();
                recalculateTotals();
            });
        }

        function recalculateTotals() {
            let subtotal = 0;
            const rows = document.querySelectorAll('.item-row');

            rows.forEach(row => {
                const select = row.querySelector('.product-select');
                const qtyInput = row.querySelector('.quantity-input');
                const priceDisplay = row.querySelector('.price-display');
                const subtotalDisplay = row.querySelector('.subtotal-display');

                const selectedOption = select.options[select.selectedIndex];
                const price = parseFloat(selectedOption?.dataset?.price || 0);
                const maxStock = parseInt(selectedOption?.dataset?.stock || 0);
                let qty = parseInt(qtyInput.value || 0);

                if (qty < 1) qty = 1;

                const lineSubtotal = price * qty;
                priceDisplay.textContent = formatCOP(price);
                subtotalDisplay.textContent = formatCOP(lineSubtotal);

                subtotal += lineSubtotal;
            });

            const tax = subtotal * 0.19;
            const total = subtotal + tax;

            document.getElementById('subtotal-display').textContent = formatCOP(subtotal);
            document.getElementById('tax-display').textContent = formatCOP(tax);
            document.getElementById('total-display').textContent = formatCOP(total);
        }

        addBtn.addEventListener('click', () => addRow());

        // Reconstruir filas si hubo error de validación del backend o agregar fila inicial
        if (oldItems && oldItems.length > 0) {
            oldItems.forEach(item => addRow(item));
        } else {
            addRow(); // Fila inicial
        }
    </script>
    @endpush
</x-app-layout>
