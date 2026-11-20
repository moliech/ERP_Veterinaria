<?php

namespace Database\Seeders;

use App\Models\InventoryMovement;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommercialTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first() ?? User::first();
        $vendedor = User::where('email', 'vendedor@test.com')->first() ?? $admin;
        $almacenista = User::where('email', 'almacenista@test.com')->first() ?? $admin;

        $providers = Provider::all();
        $clients = Owner::all();
        $products = Product::all()->keyBy('barcode');

        if ($providers->isEmpty() || $clients->isEmpty() || $products->count() < 10) {
            return;
        }

        $prov1 = $providers->first();
        $prov2 = $providers->skip(1)->first() ?? $prov1;
        $prov3 = $providers->skip(2)->first() ?? $prov1;

        // =====================================================================
        // FASE 1: INICIALIZACIÓN DE KARDEX (SALDO INICIAL 30 DÍAS ATRÁS)
        // =====================================================================
        foreach ($products as $p) {
            if (! InventoryMovement::where('product_id', $p->id)->where('concept', 'inventario_inicial')->exists()) {
                InventoryMovement::create([
                    'product_id' => $p->id,
                    'type' => 'entrada',
                    'concept' => 'inventario_inicial',
                    'quantity' => $p->current_stock,
                    'balance' => $p->current_stock,
                    'unit_cost' => $p->cost_price,
                    'user_id' => $admin->id,
                    'notes' => 'Inventario inicial de apertura de tienda',
                    'created_at' => now()->subDays(30),
                    'updated_at' => now()->subDays(30),
                ]);
            }
        }

        // =====================================================================
        // FASE 2: ÓRDENES DE COMPRA HISTÓRICAS Y ACTIVAS (8 ÓRDENES)
        // =====================================================================
        $comprasData = [
            [
                'number' => 'OC-0001',
                'provider' => $prov1,
                'days' => 25,
                'status' => 'recibida',
                'notes' => 'Abastecimiento de alimento canino premium.',
                'items' => [
                    ['barcode' => '770001', 'qty' => 10, 'rec' => 10, 'cost' => 185000],
                    ['barcode' => '770002', 'qty' => 12, 'rec' => 12, 'cost' => 96000],
                ],
            ],
            [
                'number' => 'OC-0002',
                'provider' => $prov2,
                'days' => 20,
                'status' => 'parcial',
                'notes' => 'Entrega parcial en bodega; pendiente despacho de snack.',
                'items' => [
                    ['barcode' => '770003', 'qty' => 15, 'rec' => 8, 'cost' => 72000],
                    ['barcode' => '770005', 'qty' => 20, 'rec' => 20, 'cost' => 14500],
                ],
            ],
            [
                'number' => 'OC-0003',
                'provider' => $prov3,
                'days' => 16,
                'status' => 'recibida',
                'notes' => 'Línea de accesorios y camas ortopédicas.',
                'items' => [
                    ['barcode' => '770006', 'qty' => 8, 'rec' => 8, 'cost' => 36000],
                    ['barcode' => '770007', 'qty' => 5, 'rec' => 5, 'cost' => 95000],
                ],
            ],
            [
                'number' => 'OC-0004',
                'provider' => $prov1,
                'days' => 12,
                'status' => 'recibida',
                'notes' => 'Reposición de juguetes Kong y dermocosméticos.',
                'items' => [
                    ['barcode' => '770008', 'qty' => 12, 'rec' => 12, 'cost' => 31000],
                    ['barcode' => '770009', 'qty' => 10, 'rec' => 10, 'cost' => 22000],
                ],
            ],
            [
                'number' => 'OC-0005',
                'provider' => $prov2,
                'days' => 8,
                'status' => 'recibida',
                'notes' => 'Antiparasitarios NexGard y arena aglomerante.',
                'items' => [
                    ['barcode' => '770010', 'qty' => 15, 'rec' => 15, 'cost' => 54000],
                    ['barcode' => '770011', 'qty' => 14, 'rec' => 14, 'cost' => 28000],
                ],
            ],
            [
                'number' => 'OC-0006',
                'provider' => $prov3,
                'days' => 5,
                'status' => 'parcial',
                'notes' => 'Bebederos automáticos - Despacho parcial recibido.',
                'items' => [
                    ['barcode' => '770012', 'qty' => 8, 'rec' => 4, 'cost' => 74000],
                ],
            ],
            [
                'number' => 'OC-0007',
                'provider' => $prov1,
                'days' => 2,
                'status' => 'pendiente',
                'notes' => 'Orden pendiente de arribo: Royal Canin Instinctive para reabastecimiento crítico.',
                'items' => [
                    ['barcode' => '770004', 'qty' => 20, 'rec' => 0, 'cost' => 62000],
                ],
            ],
            [
                'number' => 'OC-0008',
                'provider' => $prov2,
                'days' => 1,
                'status' => 'anulada',
                'notes' => 'Orden anulada por retraso del distribuidor. Cero mercancía recibida.',
                'items' => [
                    ['barcode' => '770001', 'qty' => 5, 'rec' => 0, 'cost' => 185000],
                ],
            ],
        ];

        foreach ($comprasData as $data) {
            if (Purchase::withTrashed()->where('purchase_number', $data['number'])->exists()) {
                continue;
            }

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += ($item['qty'] * $item['cost']);
            }
            $tax = round($subtotal * 0.19, 2);
            $total = $subtotal + $tax;

            $date = now()->subDays($data['days']);

            $purchase = Purchase::create([
                'purchase_number' => $data['number'],
                'provider_id' => $data['provider']->id,
                'user_id' => $almacenista->id,
                'purchase_date' => $date->toDateString(),
                'status' => $data['status'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'received_at' => in_array($data['status'], ['recibida', 'parcial']) ? $date->addHours(4) : null,
                'notes' => $data['notes'],
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            if ($data['status'] === 'anulada') {
                $purchase->delete(); // Soft delete
            }

            foreach ($data['items'] as $item) {
                $prod = $products[$item['barcode']] ?? null;
                if (! $prod) {
                    continue;
                }

                $detailSub = $item['qty'] * $item['cost'];
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $prod->id,
                    'quantity' => $item['qty'],
                    'received_quantity' => $item['rec'],
                    'unit_cost' => $item['cost'],
                    'subtotal' => $detailSub,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // Asentar en Kardex si hubo mercancía físicamente recibida
                if ($item['rec'] > 0) {
                    $prod->refresh();
                    $newBalance = $prod->current_stock + $item['rec'];
                    $prod->update(['current_stock' => $newBalance, 'cost_price' => $item['cost']]);

                    InventoryMovement::create([
                        'product_id' => $prod->id,
                        'type' => 'entrada',
                        'concept' => 'compra',
                        'quantity' => $item['rec'],
                        'balance' => $newBalance,
                        'unit_cost' => $item['cost'],
                        'reference_type' => Purchase::class,
                        'reference_id' => $purchase->id,
                        'user_id' => $almacenista->id,
                        'notes' => "Recepción física de orden {$purchase->purchase_number}",
                        'created_at' => $date->addHours(4),
                        'updated_at' => $date->addHours(4),
                    ]);
                }
            }
        }

        // =====================================================================
        // FASE 3: VENTAS POS HISTÓRICAS EN MOSTRADOR (20 FACTURAS)
        // =====================================================================
        $ventasConfig = [
            ['days' => 24, 'items' => [['770001', 1], ['770005', 2]], 'pay' => 'efectivo', 'notes' => 'Cliente mostrador habitual.'],
            ['days' => 23, 'items' => [['770002', 1], ['770006', 1]], 'pay' => 'tarjeta', 'notes' => 'Pago POS con tarjeta débito.'],
            ['days' => 21, 'items' => [['770003', 2], ['770008', 1]], 'pay' => 'transferencia', 'notes' => 'Transferencia bancaria Nequi.'],
            ['days' => 20, 'items' => [['770010', 1], ['770011', 1]], 'pay' => 'efectivo', 'notes' => 'Antiparasitario y arena.'],
            ['days' => 19, 'items' => [['770005', 3], ['770009', 1]], 'pay' => 'tarjeta', 'notes' => 'Snacks y champú canino.'],
            ['days' => 18, 'items' => [['770001', 1], ['770008', 2]], 'pay' => 'efectivo', 'notes' => 'Alimento Taste y 2 Kongs.'],
            ['days' => 17, 'items' => [['770002', 2], ['770005', 2]], 'pay' => 'tarjeta', 'notes' => 'Bultos Chunky raza mediana.'],
            ['days' => 15, 'items' => [['770003', 1], ['770012', 1]], 'pay' => 'transferencia', 'notes' => 'Bebedero automático y alimento gato.'],
            ['days' => 14, 'items' => [['770006', 2], ['770008', 1]], 'pay' => 'efectivo', 'notes' => 'Arnés reflectivo y juguete.'],
            ['days' => 13, 'items' => [['770007', 1], ['770009', 1]], 'pay' => 'tarjeta', 'notes' => 'Cama ortopédica XL y champú.'],
            ['days' => 11, 'items' => [['770010', 2], ['770005', 4]], 'pay' => 'efectivo', 'notes' => 'NexGard Spectra y snacks de hígado.'],
            ['days' => 10, 'items' => [['770001', 1], ['770011', 2]], 'pay' => 'tarjeta', 'notes' => 'Alimento y arena sanitaria.'],
            ['days' => 9, 'items' => [['770002', 1], ['770004', 1]], 'pay' => 'efectivo', 'notes' => 'Chunky y sobre Royal Canin.'],
            ['days' => 8, 'items' => [['770003', 1], ['770008', 1]], 'pay' => 'tarjeta', 'notes' => 'Pro Plan y juguete Kong.'],
            ['days' => 6, 'items' => [['770005', 2], ['770006', 1]], 'pay' => 'efectivo', 'notes' => 'Snacks y arnés talla L.'],
            ['days' => 5, 'items' => [['770010', 1], ['770009', 1]], 'pay' => 'transferencia', 'notes' => 'NexGard y champú avena.'],
            ['days' => 4, 'items' => [['770001', 1], ['770002', 1]], 'pay' => 'tarjeta', 'notes' => 'Combo bultos caninos.'],
            ['days' => 3, 'items' => [['770007', 1], ['770011', 1]], 'pay' => 'efectivo', 'notes' => 'Cama y arena sanitaria.'],
            ['days' => 2, 'items' => [['770003', 1], ['770005', 2]], 'pay' => 'tarjeta', 'notes' => 'Venta mostrador fin de semana.'],
            ['days' => 1, 'items' => [['770008', 1], ['770010', 1]], 'pay' => 'efectivo', 'notes' => 'Venta rápida POS tarde.'],
        ];

        $invoiceNum = 1;
        foreach ($ventasConfig as $cfg) {
            $formattedNum = 'FAC-'.str_pad($invoiceNum, 5, '0', STR_PAD_LEFT);
            $invoiceNum++;

            if (Sale::withTrashed()->where('invoice_number', $formattedNum)->exists()) {
                continue;
            }

            $date = now()->subDays($cfg['days'])->setTime(11 + ($invoiceNum % 6), 15 * ($invoiceNum % 4));
            $client = $clients->random();

            $subtotal = 0;
            $tax = 0;
            $saleLines = [];

            foreach ($cfg['items'] as $item) {
                $prod = $products[$item[0]] ?? null;
                if (! $prod || $prod->current_stock < $item[1]) {
                    continue;
                }

                $lineSub = (float) $prod->sale_price * $item[1];
                $taxRate = (float) ($prod->tax_rate ?? 19.00);
                $lineTax = round($lineSub * ($taxRate / 100), 2);

                $subtotal += $lineSub;
                $tax += $lineTax;

                $saleLines[] = [
                    'product' => $prod,
                    'quantity' => $item[1],
                    'unit_price' => $prod->sale_price,
                    'subtotal' => $lineSub,
                ];
            }

            if (empty($saleLines)) {
                continue;
            }

            $total = $subtotal + $tax;

            $sale = Sale::create([
                'invoice_number' => $formattedNum,
                'client_id' => $client->id,
                'user_id' => $vendedor->id,
                'sale_date' => $date->toDateString(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'pagada',
                'notes' => $cfg['notes'],
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($saleLines as $line) {
                $prod = $line['product'];
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $prod->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'subtotal' => $line['subtotal'],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // Descontar inventario y asentar en Kardex
                $prod->refresh();
                $newBalance = max(0, $prod->current_stock - $line['quantity']);
                $prod->update(['current_stock' => $newBalance]);

                InventoryMovement::create([
                    'product_id' => $prod->id,
                    'type' => 'salida',
                    'concept' => 'venta',
                    'quantity' => $line['quantity'],
                    'balance' => $newBalance,
                    'unit_cost' => $prod->cost_price,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'user_id' => $vendedor->id,
                    'notes' => "Venta POS {$sale->invoice_number} en mostrador",
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}
