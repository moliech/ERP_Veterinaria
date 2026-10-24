<?php

namespace App\Providers;

use App\Models\CompanySetting;
use App\Models\SystemModule;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Interceptor global de habilidades (Gate::before):
         * Reemplaza el evaluador por defecto de Spatie.
         * 1. Rol "admin" siempre tiene acceso total (bypass).
         * 2. Los demás usuarios se evalúan con hasPermissionInActiveRole():
         *    permiso efectivo = permisos directos + permisos del rol activo.
         */
        Gate::before(function (User $user, string $ability): ?bool {
            if (! $user->active) {
                return false;
            }

            if ($user->hasRole('admin')) {
                return true;
            }

            // true = autorizado. null = «no opino»: Laravel sigue con Policies/Gates y, si no hay, niega.
            return $user->hasPermissionInActiveRole($ability) ? true : null;
        });

        // Compartir Identidad Corporativa y estado de Módulos con todas las vistas y PDFs
        View::composer('*', function ($view) {
            $view->with('company', CompanySetting::current());
            $view->with('enabledModules', SystemModule::enabledMap());
        });
    }
}
