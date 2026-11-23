<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_core_erp_routes_and_exports_return_ok_without_404_or_500(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create(['active' => true]);
        $admin->assignRole('admin');

        $category = Category::create(['name' => 'Alimento Seco', 'description' => 'Test']);
        $product = Product::create([
            'category_id' => $category->id,
            'barcode' => '7700001112223',
            'name' => 'Alimento Premium 10kg',
            'cost_price' => 50000,
            'sale_price' => 80000,
            'tax_rate' => 19.00,
            'current_stock' => 20,
            'minimum_stock' => 5,
            'active' => true,
        ]);
        $client = Owner::create([
            'document_type' => 'CC',
            'document_number' => '1000200030',
            'first_name' => 'Laura',
            'last_name' => 'Gómez',
            'email' => 'laura@test.com',
            'city' => 'Cartago',
        ]);
        $provider = Provider::create([
            'company_name' => 'Distribuidora Mascotas SAS',
            'nit' => '900111222-3',
            'contact_name' => 'Carlos Ruiz',
            'active' => true,
        ]);

        $annulledSale = Sale::create([
            'invoice_number' => 'FAC-00099',
            'client_id' => $client->id,
            'user_id' => $admin->id,
            'sale_date' => now()->toDateString(),
            'subtotal' => 80000,
            'tax' => 15200,
            'total' => 95200,
            'status' => 'anulada',
        ]);
        $annulledSale->delete();

        $annulledPurchase = Purchase::create([
            'purchase_number' => 'OC-00099',
            'provider_id' => $provider->id,
            'user_id' => $admin->id,
            'purchase_date' => now()->toDateString(),
            'subtotal' => 100000,
            'tax' => 19000,
            'total' => 119000,
            'status' => 'anulada',
        ]);
        $annulledPurchase->delete();

        $routes = [
            route('dashboard'),
            route('products.index'),
            route('products.create'),
            route('products.edit', $product),
            route('categories.index'),
            route('categories.create'),
            route('categories.edit', $category),
            route('clients.index'),
            route('clients.create'),
            route('clients.edit', $client),
            route('providers.index'),
            route('providers.create'),
            route('providers.edit', $provider),
            route('pricing.index'),
            route('sales.index'),
            route('sales.create'),
            route('sales.show', $annulledSale),
            route('sales.pdf', $annulledSale),
            route('purchases.index'),
            route('purchases.create'),
            route('purchases.show', $annulledPurchase),
            route('purchases.pdf', $annulledPurchase),
            route('inventory.kardex'),
            route('inventory.pdf', $product),
            route('inventory.adjust', $product),
            route('products.export'),
            route('products.template'),
            route('products.import.form'),
            route('sales.export'),
            route('audit.index'),
            route('notifications.index'),
            route('settings.index'),
            route('settings.users.index'),
            route('settings.users.create'),
            route('settings.roles.index'),
            route('settings.roles.create'),
            route('settings.permissions.index'),
            route('settings.modules.index'),
            route('profile.edit'),
        ];

        foreach ($routes as $url) {
            $this->actingAs($admin)
                ->get($url)
                ->assertOk();
        }
    }
}
