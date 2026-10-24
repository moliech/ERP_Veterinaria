<?php

namespace Tests\Feature;

use App\Models\User;
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
}
