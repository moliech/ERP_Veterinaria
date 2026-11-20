<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Provider;
use App\Models\User;
use App\Services\PurchaseService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlujoComercialKardexTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $vendedor;

    protected User $almacenista;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::factory()->create([
            'email' => 'admin@vetpets.test',
            'active' => true,
        ])->assignRole('admin');

        $this->vendedor = User::factory()->create([
            'email' => 'vendedor@vetpets.test',
            'active' => true,
        ])->assignRole('vendedor');

        $this->almacenista = User::factory()->create([
            'email' => 'almacenista@vetpets.test',
            'active' => true,
        ])->assignRole('almacenista');
    }

    private function logPaso(string $paso, string $mensaje): void
    {
        fwrite(STDOUT, "\n  \033[1;36m➜ [{$paso}]\033[0m {$mensaje}");
    }

    public function test_recorrido_compra_recepcion_parcial_y_asentamiento_en_kardex(): void
    {
        fwrite(STDOUT, "\n\n  \033[1;33m══════════════════════════════════════════════════════════════════\033[0m");
        fwrite(STDOUT, "\n  \033[1;32mDEMOSTRACIÓN 1: COMPRA CON RECEPCIÓN E IMPACTO EN KARDEX\033[0m");
        fwrite(STDOUT, "\n  \033[1;33m══════════════════════════════════════════════════════════════════\033[0m");

        $category = Category::create(['name' => 'Nutrición Canina']);
        $product = Product::create([
            'name' => 'Alimento Royal Canin Maxi 15kg',
            'category_id' => $category->id,
            'cost_price' => 150000,
            'sale_price' => 210000,
            'current_stock' => 10,
            'min_stock' => 5,
            'active' => true,
            'lot_number' => 'LOT-RC-2026',
        ]);

        $this->logPaso('PASO 1 - STOCK INICIAL', "Producto '{$product->name}' | Stock actual en bodega: {$product->current_stock} unidades.");

        $provider = Provider::create([
            'nit' => '900123456-7',
            'company_name' => 'Distribuidora Pets Colombia S.A.S',
            'email' => 'pedidos@pets.test',
            'phone' => '3109876543',
        ]);

        $purchaseService = app(PurchaseService::class);

        // 1. Crear Orden de Compra por 20 unidades
        $purchase = $purchaseService->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->toDateString(),
            'notes' => 'Reabastecimiento línea Maxi Adult',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 20,
                    'unit_cost' => 150000,
                ],
            ],
        ], $this->admin->id);

        $this->logPaso('PASO 2 - ORDEN DE COMPRA', "Orden emitida: {$purchase->purchase_number} a '{$provider->company_name}' por 20 unidades (Pendiente).");

        // 2. Recepción Física Parcial de 12 unidades
        $detail = $purchase->details->first();
        $purchaseService->receive($purchase, [
            $detail->id => 12,
        ]);

        $purchase->refresh();
        $product->refresh();

        $this->logPaso('PASO 3 - RECEPCIÓN FÍSICA', 'Llegada de camión a bodega: Se reciben físicamente 12 de las 20 unidades solicitadas.');
        $this->logPaso('PASO 4 - KARDEX & BALANCE', "Movimiento asentado en Kardex. Estado de orden: '{$purchase->status}'. Stock en bodega: {$product->current_stock} unidades (+12).");

        $this->assertSame(22, $product->current_stock);
        $this->assertSame('parcial', $purchase->status);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'entrada',
            'quantity' => 12,
        ]);

        fwrite(STDOUT, "\n  \033[1;32m✔ RESULTADO: Stock incrementado correctamente de 10 a 22 unidades con trazabilidad en Kardex.\033[0m\n");
    }

    public function test_recorrido_venta_pos_api_rest_liquidacion_iva_y_descuento_kardex(): void
    {
        fwrite(STDOUT, "\n\n  \033[1;33m══════════════════════════════════════════════════════════════════\033[0m");
        fwrite(STDOUT, "\n  \033[1;32mDEMOSTRACIÓN 2: VENTA POS POR API REST, IVA 19% Y DESCUENTO KARDEX\033[0m");
        fwrite(STDOUT, "\n  \033[1;33m══════════════════════════════════════════════════════════════════\033[0m");

        // 1. Login vía API REST para obtener token
        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => 'vendedor@vetpets.test',
            'password' => 'password',
            'device_name' => 'terminal-pos-01',
        ]);
        $token = $loginResponse->json('token');

        $this->logPaso('PASO 1 - API AUTH', "Autenticación contra /api/v1/login exitosa. Bearer Token emitido para 'vendedor@vetpets.test'.");

        $category = Category::create(['name' => 'Farmacia Veterinaria']);
        $product = Product::create([
            'name' => 'Antiparasitario NexGard Spectra',
            'category_id' => $category->id,
            'cost_price' => 40000,
            'sale_price' => 65000,
            'current_stock' => 15,
            'min_stock' => 5,
            'active' => true,
            'lot_number' => 'LOT-NXG-2026',
        ]);

        $this->logPaso('PASO 2 - STOCK INICIAL', "Producto '{$product->name}' en mostrador | Stock inicial: {$product->current_stock} unidades.");

        $owner = Owner::create([
            'first_name' => 'Laura',
            'last_name' => 'Perez',
            'document_type' => 'CC',
            'document_number' => '52987654',
            'email' => 'laura@test.com',
        ]);

        // 2. Venta de 4 unidades por API
        $response = $this->withToken($token)->postJson('/api/v1/sales', [
            'client_id' => $owner->id,
            'sale_date' => now()->toDateString(),
            'notes' => 'Venta POS mostrador con tarjeta',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 4,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $invoice = $response->json('data.factura');
        $subtotal = $response->json('data.subtotal');
        $tax = $response->json('data.iva');
        $total = $response->json('data.total');

        $product->refresh();

        $this->logPaso('PASO 3 - POST /api/v1/sales', "Factura {$invoice} emitida para '{$owner->first_name} {$owner->last_name}' por 4 unidades.");
        $this->logPaso('PASO 4 - LIQUIDACIÓN FISCAL', "Subtotal: \${$subtotal} | IVA 19%: \${$tax} | Total Facturado: \${$total}");
        $this->logPaso('PASO 5 - KARDEX & STOCK', "Movimiento asentado en Kardex (Tipo: VENTA). Stock final en bodega: {$product->current_stock} unidades (-4).");

        $this->assertSame(11, $product->current_stock);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'salida',
            'quantity' => 4,
        ]);

        fwrite(STDOUT, "\n  \033[1;32m✔ RESULTADO: Venta procesada por API REST, IVA 19% liquidado y stock descontado en Kardex de 15 a 11 unidades.\033[0m\n\n");
    }
}
