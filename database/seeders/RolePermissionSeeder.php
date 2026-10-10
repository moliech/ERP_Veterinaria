<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'ver-productos', 'crear-productos', 'editar-productos', 'eliminar-productos',
            'ver-categorias', 'crear-categorias', 'editar-categorias', 'eliminar-categorias',
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores',
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-pacientes', 'crear-pacientes', 'editar-pacientes',
            'ver-ventas', 'crear-ventas', 'eliminar-ventas',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate('admin')->syncPermissions(Permission::all());
        Role::findOrCreate('vendedor')->syncPermissions([
            'ver-productos', 'ver-categorias', 'ver-clientes', 'crear-clientes', 'ver-pacientes',
            'ver-ventas', 'crear-ventas',
        ]);
        Role::findOrCreate('almacenista')->syncPermissions([
            'ver-productos', 'crear-productos', 'editar-productos', 'ver-categorias', 'crear-categorias'
        ]);
    }
}
