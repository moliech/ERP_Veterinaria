<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\SystemModule;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // Basta con UNO de los tres permisos (el | significa «o»)
            new Middleware('permission:ver-usuarios|ver-roles|ver-permisos'),
        ];
    }

    public function __invoke()
    {
        SystemModule::ensureSeeded();

        return view('settings.index', [
            'usersCount' => User::count(),
            'inactiveCount' => User::where('active', false)->count(),
            'multiRoleCount' => User::has('roles', '>', 1)->count(),
            'rolesCount' => Role::count(),
            'permissionsCount' => Permission::count(),
            'company' => CompanySetting::current(),
            'modulesTotal' => SystemModule::count(),
            'modulesEnabled' => SystemModule::where('is_enabled', true)->count(),
        ]);
    }
}
