<x-app-layout> 
    <x-slot name="header"> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración — 
Seguridad</h2> 

</x-slot> 
 
    <div class="py-6"> 
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6"> 
 
            <div class="bg-white shadow rounded p-4 text-sm text-gray-700"> 
                <strong>¿Cómo se calculan los permisos?</strong> 
                Permisos efectivos = permisos del <strong>rol activo</strong> + permisos 
<strong>directos</strong> del usuario. 
                Un usuario con varios roles elige con cuál trabajar al iniciar sesión. 
            </div> 
 
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4"> 
                @can('ver-usuarios') 
                    <a href="{{ route('settings.users.index') }}" class="bg-white shadow rounded p-6 
hover:shadow-lg transition"> 
                        <h3 class="font-semibold text-lg text-blue-700">Usuarios</h3> 
                        <p class="text-3xl font-bold mt-2">{{ $usersCount }}</p> 
                        <p class="text-sm text-gray-600 mt-1">{{ $inactiveCount }} inactivos · {{ 
$multiRoleCount }} con varios roles</p> 
                    </a> 
                @endcan 
                @can('ver-roles') 
                    <a href="{{ route('settings.roles.index') }}" class="bg-white shadow rounded p-6 
hover:shadow-lg transition"> 
                        <h3 class="font-semibold text-lg text-blue-700">Roles</h3> 
                        <p class="text-3xl font-bold mt-2">{{ $rolesCount }}</p> 
                        <p class="text-sm text-gray-600 mt-1">Perfiles de trabajo y sus permisos.</p> 
                    </a> 
                @endcan 
                @can('ver-permisos') 
                    <a href="{{ route('settings.permissions.index') }}" class="bg-white shadow rounded p-
6 hover:shadow-lg transition"> 
                        <h3 class="font-semibold text-lg text-blue-700">Matriz de permisos</h3> 
                        <p class="text-3xl font-bold mt-2">{{ $permissionsCount }}</p> 
                        <p class="text-sm text-gray-600 mt-1">Permisos por rol, módulo y usuario.</p> 
                    </a> 
                @endcan 
            </div> 
        </div> 
    </div> 
</x-app-layout>