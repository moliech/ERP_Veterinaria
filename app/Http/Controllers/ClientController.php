<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:10'],
            'document_number' => ['required', 'string', 'max:20', 'unique:owners,document_number'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        Owner::create($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Owner $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Owner $client)
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:10'],
            'document_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('owners', 'document_number')->ignore($client->id),
            ],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $client->update($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
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
