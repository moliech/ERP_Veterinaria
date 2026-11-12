<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function getToken(string $email, ?string $role = null): string
    {
        $response = $this->postJson('/api/v1/login', array_filter([
            'email'       => $email,
            'password'    => 'password',
            'device_name' => 'test-device',
            'role'        => $role,
        ]));

        $response->assertStatus(201);

        return $response->json('token');
    }

    public function test_login_y_deteccion_de_rol_activo_por_token(): void
    {
        // Credenciales incorrectas
        $this->postJson('/api/v1/login', [
            'email'    => 'admin@test.com',
            'password' => 'clave-erronea',
        ])->assertStatus(422)->assertJsonValidationErrors('email');

        // Usuario con múltiples roles sin especificar rol
        $this->postJson('/api/v1/login', [
            'email'    => 'supervisor@test.com',
            'password' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors('role');

        // Login supervisor con rol vendedor
        $token = $this->getToken('supervisor@test.com', 'vendedor');

        // Endpoint /api/v1/me
        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('rol_activo', 'vendedor')
            ->assertJsonPath('email', 'supervisor@test.com');
    }

    public function test_catalogo_de_productos_por_api(): void
    {
        // Sin token -> 401 Unauthorized
        $this->getJson('/api/v1/products')->assertUnauthorized();

        $token = $this->getToken('vendedor@test.com');

        // Listado de productos con token
        $this->withToken($token)
            ->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nombre', 'precio', 'stock', 'disponible'],
                ],
            ]);

        $product = Product::first();
        $this->assertNotNull($product);

        // Ver detalle de un producto
        $this->withToken($token)
            ->getJson("/api/v1/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.nombre', $product->name);

        // Producto no existente -> 404
        $this->withToken($token)
            ->getJson('/api/v1/products/999999')
            ->assertNotFound();
    }

    public function test_vendedor_puede_registrar_venta_transaccional_con_kardex(): void
    {
        $vendedorToken = $this->getToken('vendedor@test.com');

        $product = Product::where('current_stock', '>', 5)->first();
        $this->assertNotNull($product);
        $client = Owner::first();
        $this->assertNotNull($client);

        // Venta con items vacíos -> 422
        $this->withToken($vendedorToken)
            ->postJson('/api/v1/sales', [
                'client_id' => $client->id,
                'sale_date' => now()->toDateString(),
                'items'     => [],
            ])->assertStatus(422)->assertJsonValidationErrors('items');

        // Venta exitosa
        $initialStock = $product->current_stock;
        $response = $this->withToken($vendedorToken)
            ->postJson('/api/v1/sales', [
                'client_id' => $client->id,
                'sale_date' => now()->toDateString(),
                'notes'     => 'Venta de prueba automatizada API',
                'items'     => [
                    ['product_id' => $product->id, 'quantity' => 2],
                ],
            ])->assertStatus(201);

        $response->assertJsonStructure([
            'data' => ['id', 'factura', 'fecha', 'estado', 'subtotal', 'iva', 'total', 'pdf'],
        ]);

        $this->assertSame($initialStock - 2, $product->fresh()->current_stock);

        // Consultar la venta creada
        $saleId = $response->json('data.id');
        $this->withToken($vendedorToken)
            ->getJson("/api/v1/sales/{$saleId}")
            ->assertOk()
            ->assertJsonPath('data.id', $saleId);
    }

    public function test_almacenista_no_puede_crear_ventas_en_api(): void
    {
        $almacenistaToken = $this->getToken('almacenista@test.com');
        $product = Product::first();
        $client = Owner::first();

        // Almacenista no tiene permiso crear-ventas -> 403 Forbidden
        $this->withToken($almacenistaToken)
            ->postJson('/api/v1/sales', [
                'client_id' => $client->id,
                'sale_date' => now()->toDateString(),
                'items'     => [
                    ['product_id' => $product->id, 'quantity' => 1],
                ],
            ])->assertForbidden();
    }

    public function test_logout_revoca_el_token(): void
    {
        $token = $this->getToken('vendedor@test.com');
        $user = User::where('email', 'vendedor@test.com')->first();

        $this->assertSame(1, $user->tokens()->count());

        $this->withToken($token)
            ->postJson('/api/v1/logout')
            ->assertOk();

        // El token se eliminó de la base de datos
        $this->assertSame(0, $user->tokens()->count());

        // Al intentar usar el token revocado en una nueva petición
        app('auth')->forgetGuards();
        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertUnauthorized();
    }
}
