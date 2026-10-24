<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SystemModule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SystemModuleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-usuarios|ver-roles|ver-permisos'),
        ];
    }

    public function index()
    {
        SystemModule::ensureSeeded();
        $modules = SystemModule::orderBy('id')->get();
        $labelsByKey = $modules->pluck('name', 'key');

        return view('settings.modules.index', [
            'modules' => $modules,
            'labelsByKey' => $labelsByKey,
        ]);
    }

    public function toggle(SystemModule $module)
    {
        if ($module->is_core) {
            return back()->with('error', "El módulo «{$module->name}» es parte del núcleo del sistema y no puede deshabilitarse.");
        }

        $allModules = SystemModule::all()->keyBy('key');

        if ($module->is_enabled) {
            // Regla 1: Verificar que ningún otro módulo activo dependa de este
            $dependents = $allModules->filter(function (SystemModule $other) use ($module) {
                return $other->is_enabled && in_array($module->key, $other->depends_on ?? [], true);
            })->pluck('name');

            if ($dependents->isNotEmpty()) {
                return back()->with(
                    'error',
                    "No se puede deshabilitar «{$module->name}» porque es requerido por los siguientes módulos activos: ".$dependents->join(', ').'.'
                );
            }
        } else {
            // Regla 2: Verificar que los módulos de los que depende estén activos
            $missing = collect($module->depends_on ?? [])
                ->filter(fn ($depKey) => isset($allModules[$depKey]) && ! $allModules[$depKey]->is_enabled)
                ->map(fn ($depKey) => $allModules[$depKey]->name);

            if ($missing->isNotEmpty()) {
                return back()->with(
                    'error',
                    "Para activar «{$module->name}» primero debe habilitar sus módulos base requeridos: ".$missing->join(', ').'.'
                );
            }
        }

        $module->update(['is_enabled' => ! $module->is_enabled]);
        SystemModule::flushCache();

        $stateLabel = $module->is_enabled ? 'habilitado' : 'deshabilitado (datos históricos preservados)';

        return back()->with('success', "Módulo «{$module->name}» {$stateLabel}.");
    }
}
