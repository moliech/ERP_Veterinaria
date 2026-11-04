<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Provider;
use App\Models\Product;
use App\Services\PurchaseService;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\ReceivePurchaseRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-compras', only: ['index', 'show', 'pdf']),
            new Middleware('permission:crear-compras', only: ['create', 'store']),
            new Middleware('permission:recibir-compras', only: ['receive']),
            new Middleware('permission:anular-compras', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $purchases = Purchase::withTrashed()
            ->with(['provider', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->input('search') . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('purchase_number', 'like', $term)
                      ->orWhereHas('provider', function ($q) use ($term) {
                          $q->where('company_name', 'like', $term)
                            ->orWhere('nit', 'like', $term);
                      });
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('purchase_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('purchase_date', '<=', $request->input('to')))
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $totalMonth = Purchase::whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->where('status', '!=', 'anulada')
            ->sum('total');

        $pendingCount = Purchase::whereIn('status', ['pendiente', 'parcial'])->count();

        return view('purchases.index', compact('purchases', 'totalMonth', 'pendingCount'));
    }

    public function create()
    {
        $providers = Provider::orderBy('company_name')->get(['id', 'company_name', 'nit']);
        $products = Product::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'cost_price', 'sale_price', 'current_stock', 'minimum_stock']);

        return view('purchases.create', compact('providers', 'products'));
    }

    public function store(StorePurchaseRequest $request, PurchaseService $purchaseService)
    {
        try {
            $purchase = $purchaseService->register($request->validated(), $request->user()->id);

            return redirect()
                ->route('purchases.show', $purchase)
                ->with('success', "Orden de compra «{$purchase->purchase_number}» generada exitosamente.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['provider', 'user', 'details.product']);

        return view('purchases.show', compact('purchase'));
    }

    public function receive(ReceivePurchaseRequest $request, Purchase $purchase, PurchaseService $purchaseService)
    {
        try {
            $purchaseService->receive($purchase, $request->input('received', []));

            return redirect()
                ->route('purchases.show', $purchase)
                ->with('success', 'Mercancía recepcionada e inventario actualizado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    public function destroy(Purchase $purchase, PurchaseService $purchaseService)
    {
        try {
            $purchaseService->cancel($purchase);

            return redirect()
                ->route('purchases.index')
                ->with('success', "Orden de compra «{$purchase->purchase_number}» anulada correctamente.");
        } catch (ValidationException $e) {
            return back()->with('error', $e->getMessage() ?: 'No se pudo anular la orden de compra.');
        }
    }

    public function pdf(Purchase $purchase)
    {
        $purchase->load(['provider', 'user', 'details.product']);

        $pdf = Pdf::loadView('pdf.purchase', compact('purchase'));

        return $pdf->stream('Orden_Compra_' . $purchase->purchase_number . '.pdf');
    }
}
