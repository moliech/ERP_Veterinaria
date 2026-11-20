<?php

namespace Tests\Feature;

use App\Events\PurchaseReceived;
use App\Events\SaleRegistered;
use App\Models\Category;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\DailySummaryNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\PurchaseReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Clase14Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'administrador']);
        $buyerRole = Role::firstOrCreate(['name' => 'comprador']);

        $permReportes = Permission::firstOrCreate(['name' => 'ver-reportes']);
        $permCompras = Permission::firstOrCreate(['name' => 'crear-compras']);
        $permVentas = Permission::firstOrCreate(['name' => 'crear-ventas']);

        $adminRole->givePermissionTo([$permReportes, $permCompras, $permVentas]);
        $buyerRole->givePermissionTo([$permCompras]);

        $this->admin = User::factory()->create([
            'email' => 'admin@vetpets.test',
            'active' => true,
        ]);
        $this->admin->assignRole('administrador');

        $this->buyer = User::factory()->create([
            'email' => 'comprador@vetpets.test',
            'active' => true,
        ]);
        $this->buyer->assignRole('comprador');
    }

    public function test_evento_sale_registered_dispara_alerta_stock_bajo_cuando_cruza_umbral(): void
    {
        Notification::fake();

        $category = Category::create(['name' => 'Alimentos']);
        $product = Product::create([
            'name' => 'Concentrado Premium Perros',
            'category_id' => $category->id,
            'cost_price' => 50000,
            'sale_price' => 80000,
            'current_stock' => 4, // Ya quedó en 4 (umbral es 5)
            'min_stock' => 5,
            'active' => true,
        ]);

        $owner = Owner::create([
            'first_name' => 'Carlos',
            'last_name' => 'Gomez',
            'document_type' => 'CC',
            'document_number' => '12345678',
            'email' => 'carlos@test.com',
        ]);

        $sale = Sale::create([
            'invoice_number' => 'FAC-TEST-001',
            'client_id' => $owner->id,
            'user_id' => $this->admin->id,
            'sale_date' => now()->toDateString(),
            'subtotal' => 80000,
            'tax' => 15200,
            'total' => 95200,
            'status' => 'pagada',
        ]);

        $sale->details()->create([
            'product_id' => $product->id,
            'quantity' => 3, // Antes estaba en 4 + 3 = 7 (> umbral 5)
            'unit_price' => 80000,
            'subtotal' => 240000,
        ]);

        // Disparar evento
        event(new SaleRegistered($sale));

        // Debe notificar al comprador y admin que tienen el permiso 'crear-compras'
        Notification::assertSentTo(
            [$this->admin, $this->buyer],
            LowStockNotification::class,
            function ($notification) use ($product) {
                return $notification->product->id === $product->id;
            }
        );
    }

    public function test_evento_purchase_received_notifica_al_usuario_creador(): void
    {
        Notification::fake();

        $provider = Provider::create([
            'nit' => '900111222-1',
            'company_name' => 'Distribuidora Animal S.A.S',
            'email' => 'ventas@distribuidora.test',
        ]);

        $purchase = Purchase::create([
            'purchase_number' => 'OC-2026-001',
            'provider_id' => $provider->id,
            'user_id' => $this->admin->id,
            'purchase_date' => now()->toDateString(),
            'status' => 'recibida',
            'subtotal' => 500000,
            'tax' => 95000,
            'total' => 595000,
        ]);

        event(new PurchaseReceived($purchase, true));

        Notification::assertSentTo(
            $this->admin,
            PurchaseReceivedNotification::class,
            function ($notification) use ($purchase) {
                return $notification->purchase->id === $purchase->id && $notification->complete === true;
            }
        );
    }

    public function test_comando_resumen_diario_ejecuta_y_notifica_a_usuarios_con_permiso_reportes(): void
    {
        Notification::fake();

        $this->artisan('erp:resumen-diario')
            ->assertSuccessful();

        Notification::assertSentTo(
            $this->admin,
            DailySummaryNotification::class
        );
    }

    public function test_usuario_puede_acceder_al_centro_de_notificaciones_y_marcar_como_leidas(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create(['name' => 'Fármacos']);
        $product = Product::create([
            'name' => 'Antiparasitario Canino',
            'category_id' => $category->id,
            'cost_price' => 10000,
            'sale_price' => 20000,
            'current_stock' => 2,
            'min_stock' => 5,
            'active' => true,
        ]);

        // Crear una notificación en base de datos para el admin
        $this->admin->notify(new LowStockNotification($product));

        $this->assertEquals(1, $this->admin->unreadNotifications()->count());

        // Visitar la bandeja de notificaciones
        $response = $this->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSee('Centro de Notificaciones');
        $response->assertSee('Antiparasitario Canino');

        // Marcar todas como leídas
        $postResponse = $this->post(route('notifications.read-all'));
        $postResponse->assertRedirect();

        $this->assertEquals(0, $this->admin->fresh()->unreadNotifications()->count());
    }
}
