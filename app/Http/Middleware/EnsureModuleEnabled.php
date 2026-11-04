<?php

namespace App\Http\Middleware;

use App\Models\SystemModule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    /**
     * Impide el acceso por URL a un módulo que haya sido deshabilitado en Gestión de Módulos,
     * conservando intactos todos sus registros históricos en la base de datos.
     */
    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        if (! SystemModule::isEnabled($moduleKey)) {
            $module = SystemModule::where('key', $moduleKey)->first();
            $label = $module?->name ?? ucfirst($moduleKey);

            return redirect()->route('dashboard')
                ->with('error', "El módulo «{$label}» se encuentra deshabilitado actualmente en Gestión de Módulos.");
        }

        return $next($request);
    }
}
