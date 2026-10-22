<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Http\Requests\StoreProviderRequest;
use App\Http\Requests\UpdateProviderRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProviderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-proveedores', only: ['index']),
            new Middleware('permission:crear-proveedores', only: ['create', 'store']),
            new Middleware('permission:editar-proveedores', only: ['edit', 'update']),
            new Middleware('permission:eliminar-proveedores', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $providers = Provider::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->input('search') . '%';
                $q->where('company_name', 'like', $term)
                  ->orWhere('nit', 'like', $term)
                  ->orWhere('contact_person', 'like', $term);
            })
            ->withCount('purchases')
            ->orderBy('company_name')
            ->paginate(10)
            ->withQueryString();

        return view('providers.index', compact('providers'));
    }

    public function create()
    {
        return view('providers.create');
    }

    public function store(StoreProviderRequest $request)
    {
        Provider::create($request->validated());
        return redirect()->route('providers.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    public function edit(Provider $provider)
    {
        return view('providers.edit', compact('provider'));
    }

    public function update(UpdateProviderRequest $request, Provider $provider)
    {
        $provider->update($request->validated());
        return redirect()->route('providers.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Provider $provider)
    {
        // Regla obligatoria: no eliminar proveedor con compras asociadas
        if ($provider->purchases()->exists()) {
            return back()->with('error', 'No se puede eliminar el proveedor porque tiene órdenes de compra asociadas en el sistema.');
        }

        $provider->delete();
        return redirect()->route('providers.index')->with('success', 'Proveedor enviado a la papelera.');
    }
}
