<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Generar Nueva Orden de Compra') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">El stock aumentará automáticamente cuando se registre la recepción en bodega</p>
            </div>
            <a href="{{ route('purchases.index') }}"
               class="text-sm font-semibold text-slate-600 hover:text-slate-900 flex items-center space-x-1">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Volver a Compras</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm" class="space-y-6">
                @csrf

                <!-- Cabecera de la Orden -->
                <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                        1. Información General y Proveedor
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="provider_id" :value="__('Proveedor / Laboratorio *')" />
                            <select id="provider_id" name="provider_id" required
                                    class="mt-1 block w-full border-slate-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">-- Seleccione un Proveedor --</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>
                                        {{ $provider->company_name }} (NIT: {{ $provider->nit }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('provider_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="purchase_date" :value="__('Fecha de la Orden *')" />
                            <x-text-input id="purchase_date" class="block mt-1 w-full" type="date" name="purchase_date"
                                          :value="old('purchase_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('purchase_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" :value="__('Observaciones / Condiciones de Pago')" />
                            <x-text-input id="notes" class="block mt-1 w-full" type="text" name="notes"
                                          :value="old('notes')" placeholder="Ej: Pago a 30 días, entrega urgente" />
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Detalle de Productos -->
                <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                            2. Medicamentos, Alimentos e Insumos a Solicitar
                        </h3>
                        <button type="button" id="addRowBtn"
                                class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 font-bold px-3 py-1.5 rounded-xl text-xs flex items-center space-x-1 transition">
                            <i class="fa-solid fa-plus"></i> <span>Agregar Producto</span>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('items')" class="mt-2" />

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm" id="itemsTable">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 text-left w-1/2">Producto</th>
                                    <th class="px-4 py-3 text-center w-28">Cantidad</th>
                                    <th class="px-4 py-3 text-right w-36">Costo Unitario ($)</th>
                                    <th class="px-4 py-3 text-right w-36">Subtotal ($)</th>
                                    <th class="px-4 py-3 text-center w-16">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white" id="itemsBody">
                                <!-- Las filas se insertan con JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumen Financiero -->
                    <div class="flex justify-end pt-4">
                        <div class="w-full sm:w-72 bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal:</span>
                                <span class="font-mono font-semibold" id="lblSubtotal">$0,00</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>IVA (19%):</span>
                                <span class="font-mono font-semibold" id="lblTax">$0,00</span>
                            </div>
                            <div class="flex justify-between text-slate-900 font-bold text-base border-t border-slate-300 pt-2">
                                <span>TOTAL:</span>
                                <span class="font-mono text-emerald-600 text-lg" id="lblTotal">$0,00</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('purchases.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">
                            Cancelar
                        </a>
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">
                            {{ __('Generar Orden de Compra') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Script para filas dinámicas y cálculos -->
    <script>
        const productsCatalog = @json($products);
        let rowIndex = 0;

        function addRow() {
            const tbody = document.getElementById('itemsBody');
            const row = document.createElement('tr');
            row.id = `row-${rowIndex}`;
            row.className = 'hover:bg-slate-50/60 transition';

            let options = '<option value="">-- Seleccionar producto --</option>';
            productsCatalog.forEach(p => {
                options += `<option value="${p.id}" data-cost="${p.cost_price}" data-stock="${p.current_stock}">${p.name} (Stock: ${p.current_stock})</option>`;
            });

            row.innerHTML = `
                <td class="px-4 py-2">
                    <select name="items[${rowIndex}][product_id]" required onchange="onProductSelect(${rowIndex})"
                            class="w-full border-slate-300 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 product-select">
                        ${options}
                    </select>
                </td>
                <td class="px-4 py-2 text-center">
                    <input type="number" name="items[${rowIndex}][quantity]" value="1" min="1" required
                           oninput="recalculateTotals()"
                           class="w-24 text-center border-slate-300 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 qty-input">
                </td>
                <td class="px-4 py-2 text-right">
                    <input type="number" step="0.01" name="items[${rowIndex}][unit_cost]" value="0.00" min="0.01" required
                           oninput="recalculateTotals()"
                           class="w-32 text-right border-slate-300 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 cost-input">
                </td>
                <td class="px-4 py-2 text-right font-mono font-semibold text-slate-800 line-subtotal">
                    $0,00
                </td>
                <td class="px-4 py-2 text-center">
                    <button type="button" onclick="removeRow(${rowIndex})"
                            class="text-rose-600 hover:text-rose-800 text-xs p-1">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(row);
            rowIndex++;
            recalculateTotals();
        }

        function removeRow(idx) {
            const row = document.getElementById(`row-${idx}`);
            if (row) {
                row.remove();
                recalculateTotals();
            }
        }

        function onProductSelect(idx) {
            const row = document.getElementById(`row-${idx}`);
            if (!row) return;

            const select = row.querySelector('.product-select');
            const costInput = row.querySelector('.cost-input');
            const selectedOpt = select.options[select.selectedIndex];

            if (selectedOpt && selectedOpt.dataset.cost) {
                costInput.value = parseFloat(selectedOpt.dataset.cost || 0).toFixed(2);
            }
            recalculateTotals();
        }

        function formatCurrency(amount) {
            return '$' + Number(amount).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function recalculateTotals() {
            let subtotal = 0;
            const rows = document.querySelectorAll('#itemsBody tr');

            rows.forEach(r => {
                const qty = parseFloat(r.querySelector('.qty-input')?.value || 0);
                const cost = parseFloat(r.querySelector('.cost-input')?.value || 0);
                const line = qty * cost;
                subtotal += line;

                const lineCell = r.querySelector('.line-subtotal');
                if (lineCell) lineCell.innerText = formatCurrency(line);
            });

            const tax = Math.round(subtotal * 0.19 * 100) / 100;
            const total = subtotal + tax;

            document.getElementById('lblSubtotal').innerText = formatCurrency(subtotal);
            document.getElementById('lblTax').innerText = formatCurrency(tax);
            document.getElementById('lblTotal').innerText = formatCurrency(total);
        }

        document.getElementById('addRowBtn').addEventListener('click', addRow);
        // Inicializar con una primera fila
        document.addEventListener('DOMContentLoaded', () => {
            addRow();
        });
    </script>
</x-app-layout>
