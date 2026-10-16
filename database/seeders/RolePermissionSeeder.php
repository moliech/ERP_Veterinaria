<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Catálogo de Permisos del ERP
        $permissions = [
            // Productos & Categorías
            'ver-productos', 'crear-productos', 'editar-productos', 'eliminar-productos', 'restaurar-productos',
            'ver-categorias', 'crear-categorias', 'editar-categorias', 'eliminar-categorias', 'restaurar-categorias',
            // Clientes / Propietarios
            'ver-clientes', 'crear-clientes', 'editar-clientes', 'eliminar-clientes',
            // Ventas POS
            'ver-ventas', 'crear-ventas', 'anular-ventas',
            // NUEVOS CLASE 10: Proveedores & Compras
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores', 'eliminar-proveedores',
            'ver-compras', 'crear-compras', 'recibir-compras', 'anular-compras',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Definición de Roles y Asignación Equitativa
        // Rol Administrador: Acceso total
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Rol Almacenista: Encargado de bodega, inventario, catálogo y abastecimiento
        $almacenista = Role::firstOrCreate(['name' => 'almacenista']);
        $almacenista->syncPermissions([
            'ver-productos', 'crear-productos', 'editar-productos',
            'ver-categorias', 'crear-categorias', 'editar-categorias',
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores',
            'ver-compras', 'crear-compras', 'recibir-compras', // Recibir compras en bodega
        ]);

        // Rol Vendedor: Ventas POS, consulta de catálogo y atención a clientes
        $vendedor = Role::firstOrCreate(['name' => 'vendedor']);
        $vendedor->syncPermissions([
            'ver-productos', 'ver-categorias',
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-ventas', 'crear-ventas', // Puede emitir facturas
        ]);
    }
}