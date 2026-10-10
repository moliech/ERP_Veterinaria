<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Owner;
use App\Models\Product;
use App\Services\SaleService;
use App\Http\Requests\StoreSaleRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller implements HasMiddleware
{
    /**
     * Definición de Middlewares de permisos de Spatie para el controlador.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-ventas', only: ['index', 'show']),
            new Middleware('permission:crear-ventas', only: ['create', 'store']),
            new Middleware('permission:eliminar-ventas', only: ['destroy']),
        ];
    }

    /**
     * Listado de ventas con búsqueda y filtros por fecha.
     */
    public function index(Request $request)
    {
        $sales = Sale::query()
            ->with(['client', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->string('search') . '%';
                $q->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', $search)
                      ->orWhereHas('client', fn ($q) => $q->where('name', 'like', $search));
                });
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('sale_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('sale_date', '<=', $request->input('to')))
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $totalMonth = Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->where('status', '!=', 'anulada')
            ->sum('total');

        return view('sales.index', compact('sales', 'totalMonth'));
    }

    /**
     * Mostrar formulario para registrar una nueva venta.
     */
    public function create()
    {
        $clients = Owner::orderBy('name')->get(['id', 'name', 'first_surname', 'document_number']);
        $products = Product::where('active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'sale_price', 'current_stock']);

        return view('sales.create', compact('clients', 'products'));
    }

    /**
     * Almacenar una venta utilizando el SaleService desacoplado.
     */
    public function store(StoreSaleRequest $request, SaleService $saleService)
    {
        try {
            // Llama al servicio aislado para registrar la venta y descontar stock
            $sale = $saleService->register($request->validated(), $request->user()->id);

            return redirect()
                ->route('sales.show', $sale)
                ->with('success', "Venta «{$sale->invoice_number}» registrada correctamente.");

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Ver el detalle / Factura de una venta.
     */
    public function show(Sale $sale)
    {
        $sale->load(['client', 'user', 'details.product']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Anular una venta utilizando el SaleService (devuelve el stock).
     */
    public function destroy(Sale $sale, SaleService $saleService)
    {
        // Llama al método de anulación y devolución de stock
        $saleService->cancel($sale);

        return redirect()
            ->route('sales.index')
            ->with('success', "Venta «{$sale->invoice_number}» anulada y stock devuelto al inventario.");
    }
}