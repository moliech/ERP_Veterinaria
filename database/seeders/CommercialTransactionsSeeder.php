<?php

namespace Database\Seeders;

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
        $provider1 = Provider::first();
        $provider2 = Provider::skip(1)->first() ?? $provider1;
        $client1 = Owner::first();
        $client2 = Owner::skip(1)->first() ?? $client1;

        if (! $admin || ! $provider1 || ! $client1 || Product::count() < 5) {
            return;
        }

        $p1 = Product::where('barcode', '770001')->first() ?? Product::first();
        $p2 = Product::where('barcode', '770002')->first() ?? Product::skip(1)->first();
        $p3 = Product::where('barcode', '770003')->first() ?? Product::skip(2)->first();
        $p4 = Product::where('barcode', '770005')->first() ?? Product::skip(3)->first();
        $p5 = Product::where('barcode', '770008')->first() ?? Product::skip(4)->first();

        // 1. Orden de Compra Recibida
        if (! Purchase::withTrashed()->where('purchase_number', 'OC-0001')->exists()) {
            $sub1 = (10 * 165000) + (12 * 85000);
            $tax1 = round($sub1 * 0.19, 2);
            $oc1 = Purchase::create([
                'purchase_number' => 'OC-0001',
                'provider_id' => $provider1->id,
                'user_id' => $admin->id,
                'purchase_date' => now()->subDays(5),
                'status' => 'recibida',
                'subtotal' => $sub1,
                'tax' => $tax1,
                'total' => $sub1 + $tax1,
                'received_at' => now()->subDays(4),
                'notes' => 'Abastecimiento mensual de línea de nutrición canina premium.',
            ]);
            PurchaseDetail::create([
                'purchase_id' => $oc1->id,
                'product_id' => $p1->id,
                'quantity' => 10,
                'received_quantity' => 10,
                'unit_cost' => 165000,
                'subtotal' => 1650000,
            ]);
            PurchaseDetail::create([
                'purchase_id' => $oc1->id,
                'product_id' => $p2->id,
                'quantity' => 12,
                'received_quantity' => 12,
                'unit_cost' => 85000,
                'subtotal' => 1020000,
            ]);
        }

        // 2. Orden de Compra con Recepción Parcial
        if (! Purchase::withTrashed()->where('purchase_number', 'OC-0002')->exists()) {
            $sub2 = (15 * 62000) + (20 * 12000);
            $tax2 = round($sub2 * 0.19, 2);
            $oc2 = Purchase::create([
                'purchase_number' => 'OC-0002',
                'provider_id' => $provider2->id,
                'user_id' => $admin->id,
                'purchase_date' => now()->subDays(2),
                'status' => 'parcial',
                'subtotal' => $sub2,
                'tax' => $tax2,
                'total' => $sub2 + $tax2,
                'notes' => 'Entrega parcial recibida en bodega; pendiente saldo de unidades.',
            ]);
            PurchaseDetail::create([
                'purchase_id' => $oc2->id,
                'product_id' => $p3->id,
                'quantity' => 15,
                'received_quantity' => 8,
                'unit_cost' => 62000,
                'subtotal' => 930000,
            ]);
            PurchaseDetail::create([
                'purchase_id' => $oc2->id,
                'product_id' => $p4->id,
                'quantity' => 20,
                'received_quantity' => 20,
                'unit_cost' => 12000,
                'subtotal' => 240000,
            ]);
        }

        // 3. Ventas POS del mes (para poblar KPIs y Top 5 Productos)
        if (! Sale::withTrashed()->where('invoice_number', 'FAC-0001')->exists()) {
            $sSub1 = (2 * (float) $p1->sale_price) + (4 * (float) $p4->sale_price) + (2 * (float) $p5->sale_price);
            $sTax1 = round($sSub1 * 0.19, 2);
            $sale1 = Sale::create([
                'invoice_number' => 'FAC-0001',
                'client_id' => $client1->id,
                'user_id' => $admin->id,
                'sale_date' => now()->subDays(3),
                'subtotal' => $sSub1,
                'tax' => $sTax1,
                'total' => $sSub1 + $sTax1,
                'status' => 'pagada',
                'notes' => 'Venta mostrador POS - pago con tarjeta débito.',
            ]);
            SaleDetail::create(['sale_id' => $sale1->id, 'product_id' => $p1->id, 'quantity' => 2, 'unit_price' => $p1->sale_price, 'subtotal' => 2 * $p1->sale_price]);
            SaleDetail::create(['sale_id' => $sale1->id, 'product_id' => $p4->id, 'quantity' => 4, 'unit_price' => $p4->sale_price, 'subtotal' => 4 * $p4->sale_price]);
            SaleDetail::create(['sale_id' => $sale1->id, 'product_id' => $p5->id, 'quantity' => 2, 'unit_price' => $p5->sale_price, 'subtotal' => 2 * $p5->sale_price]);
        }

        if (! Sale::withTrashed()->where('invoice_number', 'FAC-0002')->exists()) {
            $sSub2 = (3 * (float) $p2->sale_price) + (3 * (float) $p3->sale_price);
            $sTax2 = round($sSub2 * 0.19, 2);
            $sale2 = Sale::create([
                'invoice_number' => 'FAC-0002',
                'client_id' => $client2->id,
                'user_id' => $admin->id,
                'sale_date' => now()->subDay(),
                'subtotal' => $sSub2,
                'tax' => $sTax2,
                'total' => $sSub2 + $sTax2,
                'status' => 'pagada',
                'notes' => 'Venta POS cliente frecuente.',
            ]);
            SaleDetail::create(['sale_id' => $sale2->id, 'product_id' => $p2->id, 'quantity' => 3, 'unit_price' => $p2->sale_price, 'subtotal' => 3 * $p2->sale_price]);
            SaleDetail::create(['sale_id' => $sale2->id, 'product_id' => $p3->id, 'quantity' => 3, 'unit_price' => $p3->sale_price, 'subtotal' => 3 * $p3->sale_price]);
        }
    }
}
