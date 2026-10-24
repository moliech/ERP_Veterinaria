<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SystemModule;
use App\Models\User;
use Database\Seeders\CompanyAndModuleSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TestUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityConfigTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CompanyAndModuleSeeder::class);
        $this->seed(TestUserSeeder::class);
    }

    public function test_supervisor_must_select_active_role_and_segregates_permissions(): void
    {
        $supervisor = User::where('email', 'supervisor@test.com')->firstOrFail();

        // Al entrar sin rol activo en sesión, redirige a selección de rol
        $this->actingAs($supervisor)
            ->get('/dashboard')
            ->assertRedirect(route('active-role.edit'));

        // Pantalla de selección de rol carga correctamente
        $this->actingAs($supervisor)
            ->get(route('active-role.edit'))
            ->assertOk()
            ->assertSee('Seleccione con cuál desea trabajar');

        // Selecciona el rol vendedor
        $this->actingAs($supervisor)
            ->put(route('active-role.update'), ['role' => 'vendedor'])
            ->assertRedirect(route('dashboard'));

        // Con rol vendedor: puede ver ventas (200) pero NO compras (403)
        $this->actingAs($supervisor)
            ->withSession(['active_role' => 'vendedor'])
            ->get(route('sales.index'))
            ->assertOk();

        $this->actingAs($supervisor)
            ->withSession(['active_role' => 'vendedor'])
            ->get(route('purchases.index'))
            ->assertForbidden();

        // Pero conserva su permiso directo 'ver-reportes' en cualquier rol
        $this->actingAs($supervisor)
            ->withSession(['active_role' => 'vendedor']);
        $this->assertTrue($supervisor->can('ver-reportes'));

        // Cambia al rol almacenista: ahora SÍ ve compras (200) y NO ventas (403)
        $this->actingAs($supervisor)
            ->withSession(['active_role' => 'almacenista'])
            ->get(route('purchases.index'))
            ->assertOk();

        $this->actingAs($supervisor)
            ->withSession(['active_role' => 'almacenista'])
            ->get(route('sales.index'))
            ->assertForbidden();
    }

    public function test_inactive_user_cannot_login_or_keep_session(): void
    {
        $vendedor = User::where('email', 'vendedor@test.com')->firstOrFail();
        $vendedor->update(['active' => false]);

        $this->post('/login', [
            'email' => 'vendedor@test.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->actingAs($vendedor)
            ->get('/dashboard')
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_all_settings_screens_and_manage_security(): void
    {
        $admin = User::where('email', 'admin@test.com')->firstOrFail();

        $this->actingAs($admin)->get(route('settings.index'))->assertOk();
        $this->actingAs($admin)->get(route('settings.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('settings.users.create'))->assertOk();
        $this->actingAs($admin)->get(route('settings.users.edit', $admin))->assertOk();
        $this->actingAs($admin)->get(route('settings.roles.index'))->assertOk();
        $this->actingAs($admin)->get(route('settings.roles.create'))->assertOk();
        $this->actingAs($admin)->get(route('settings.permissions.index'))->assertOk();
        $this->actingAs($admin)->get(route('settings.company.edit'))->assertOk();
        $this->actingAs($admin)->get(route('settings.modules.index'))->assertOk();

        // No puede desactivarse a sí mismo
        $this->actingAs($admin)
            ->patch(route('settings.users.toggle', $admin))
            ->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->active);

        // No puede eliminar el rol admin
        $adminRole = Role::findByName('admin');
        $this->actingAs($admin)
            ->delete(route('settings.roles.destroy', $adminRole))
            ->assertSessionHas('error');

        // Crear un nuevo permiso desde el catálogo
        $this->actingAs($admin)
            ->post(route('settings.permissions.store'), [
                'action' => 'aprobar',
                'module' => 'compras',
            ])
            ->assertRedirect(route('settings.permissions.index', ['module' => 'compras']));

        $this->assertDatabaseHas('permissions', ['name' => 'aprobar-compras']);
    }

    public function test_corporate_identity_and_modular_dependencies_work(): void
    {
        // Forzar el driver 'database' (que aplica serializable_classes = false) igual que en el navegador real
        config(['cache.default' => 'database']);

        $admin = User::where('email', 'admin@test.com')->firstOrFail();

        // 1. Actualizar Identidad Corporativa
        $this->actingAs($admin)
            ->put(route('settings.company.update'), [
                'company_name' => 'Mascotas del Valle S.A.S.',
                'business_subtitle' => 'Boutique & Nutrición PetShop',
                'nit' => '901.999.888-7',
                'address' => 'Cra 5 # 12-30, Cartago',
                'phone' => '315 000 1122',
                'email' => 'contacto@mascotasvalle.co',
                'tax_rate' => 19,
                'currency' => 'COP',
                'invoice_prefix' => 'FV',
                'purchase_prefix' => 'OC',
                'primary_color' => 'indigo',
                'icon_class' => 'fa-solid fa-store',
                'invoice_footer' => 'Gracias por preferir a Mascotas del Valle.',
            ])
            ->assertRedirect(route('settings.company.edit'));

        // Verificar primera lectura (cache miss -> guarda en tabla cache) y segunda lectura (cache hit -> lee de tabla cache)
        $firstRead = CompanySetting::current();
        $secondRead = CompanySetting::current();
        $this->assertSame('Mascotas del Valle S.A.S.', $firstRead->company_name);
        $this->assertSame('Mascotas del Valle S.A.S.', $secondRead->company_name);
        $this->assertSame('indigo', $secondRead->primary_color);
        $this->assertSame('fa-solid fa-store', $secondRead->icon_class);
        $this->assertSame('Gracias por preferir a Mascotas del Valle.', $secondRead->invoice_footer);

        // Verificar que la vista renderizada muestre los valores actualizados (incluyendo View::composer('*'))
        $this->actingAs($admin)
            ->get(route('settings.company.edit'))
            ->assertOk()
            ->assertSee('Mascotas del Valle S.A.S.')
            ->assertSee('Gracias por preferir a Mascotas del Valle.');

        // 2. Intentar desactivar Productos mientras Ventas y Compras están activos -> Rechazado por dependencia
        $productsModule = SystemModule::where('key', 'products')->firstOrFail();
        $this->actingAs($admin)
            ->patch(route('settings.modules.toggle', $productsModule))
            ->assertSessionHas('error');
        $this->assertTrue($productsModule->fresh()->is_enabled);

        // 3. Desactivar el módulo de Ventas POS -> Permitido y bloquea acceso a /sales
        $salesModule = SystemModule::where('key', 'sales')->firstOrFail();
        $this->actingAs($admin)
            ->patch(route('settings.modules.toggle', $salesModule))
            ->assertSessionHas('success');
        $this->assertFalse($salesModule->fresh()->is_enabled);

        $this->actingAs($admin)
            ->get(route('sales.index'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_pricing_module_differential_tax_and_batch_expiration_control(): void
    {
        $admin = User::where('email', 'admin@test.com')->firstOrFail();

        $category = Category::create([
            'name' => 'Medicamentos y Nutrición',
            'description' => 'Categoría de prueba',
            'active' => true,
        ]);

        $client = Owner::create([
            'document_type' => 'CC',
            'document_number' => '1099888777',
            'first_name' => 'Cliente',
            'last_name' => 'Prueba Tarifas',
            'address' => 'Calle 10 # 5-20',
            'email' => 'tarifas@test.com',
        ]);

        // Producto 1: Alimento con lote vigente (costo $80.000, inicialmente 19%)
        $food = Product::create([
            'category_id' => $category->id,
            'barcode' => '779001',
            'name' => 'Concentrado Premium 10kg',
            'cost_price' => 80000,
            'sale_price' => 100000,
            'tax_rate' => 19.00,
            'batch_code' => 'LOT-VIG-01',
            'expiration_date' => now()->addMonths(6)->toDateString(),
            'current_stock' => 10,
            'minimum_stock' => 2,
            'active' => true,
        ]);

        // Producto 2: Antiparasitario exento (0% IVA) con lote vigente
        $med = Product::create([
            'category_id' => $category->id,
            'barcode' => '779002',
            'name' => 'Antiparasitario Exento Tableta',
            'cost_price' => 35000,
            'sale_price' => 50000,
            'tax_rate' => 0.00,
            'batch_code' => 'LOT-VIG-02',
            'expiration_date' => now()->addMonths(4)->toDateString(),
            'current_stock' => 10,
            'minimum_stock' => 2,
            'active' => true,
        ]);

        // 1. Verificar pantalla de Tarifas (/tarifas) y actualizar food a IVA 5% y precio $110.000
        $this->actingAs($admin)
            ->get(route('pricing.index'))
            ->assertOk()
            ->assertSee('Concentrado Premium 10kg')
            ->assertSee('LOT-VIG-01');

        $this->actingAs($admin)
            ->put(route('pricing.update', $food), [
                'sale_price' => 110000,
                'tax_rate' => 5,
            ])
            ->assertRedirect();

        $this->assertEquals(110000.0, (float) $food->fresh()->sale_price);
        $this->assertEquals(5.0, (float) $food->fresh()->tax_rate);

        // 2. Registrar venta combinando IVA 5% ($110.000 x 1 -> IVA $5.500) + IVA 0% ($50.000 x 2 -> IVA $0)
        // Subtotal esperado: $210.000 | IVA esperado: $5.500 | Total esperado: $215.500
        $this->actingAs($admin)
            ->post(route('sales.store'), [
                'client_id' => $client->id,
                'sale_date' => now()->toDateString(),
                'items' => [
                    ['product_id' => $food->id, 'quantity' => 1],
                    ['product_id' => $med->id, 'quantity' => 2],
                ],
            ])
            ->assertRedirect();

        $sale = Sale::latest('id')->firstOrFail();
        $this->assertEquals(210000.0, (float) $sale->subtotal);
        $this->assertEquals(5500.0, (float) $sale->tax);
        $this->assertEquals(215500.0, (float) $sale->total);

        // 3. Producto con lote vencido NO debe poder venderse en POS
        $expiredProduct = Product::create([
            'category_id' => $category->id,
            'barcode' => '779003',
            'name' => 'Jarabe Lote Vencido',
            'cost_price' => 20000,
            'sale_price' => 30000,
            'tax_rate' => 0.00,
            'batch_code' => 'LOT-EXP-99',
            'expiration_date' => now()->subDays(5)->toDateString(),
            'current_stock' => 8,
            'minimum_stock' => 2,
            'active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('sales.store'), [
                'client_id' => $client->id,
                'sale_date' => now()->toDateString(),
                'items' => [
                    ['product_id' => $expiredProduct->id, 'quantity' => 1],
                ],
            ])
            ->assertSessionHasErrors('items');

        $this->assertEquals(8, $expiredProduct->fresh()->current_stock);
    }
}
