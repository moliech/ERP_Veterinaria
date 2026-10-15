<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ClientController extends Controller implements HasMiddleware
{
    /**
     * Middlewares de autorización con roles y permisos de Spatie.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-clientes', only: ['index']),
            new Middleware('permission:crear-clientes', only: ['create', 'store']),
            new Middleware('permission:editar-clientes', only: ['edit', 'update']),
            new Middleware('permission:eliminar-clientes', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $clients = Owner::query()
            ->withCount('sales')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('document_number', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        Owner::create($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function edit(Owner $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Owner $client)
    {
        $client->update($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Owner $client)
    {
        if ($client->sales()->exists()) {
            return redirect()
                ->route('clients.index')
                ->with('error', 'No se puede eliminar un cliente que tiene ventas registradas.');
        }

        if ($client->pets()->exists()) {
            return redirect()
                ->route('clients.index')
                ->with('error', 'No se puede eliminar un cliente que tiene mascotas registradas.');
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
