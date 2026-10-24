<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PricingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-productos', only: ['index']),
            new Middleware('permission:editar-productos', only: ['update', 'bulkUpdate']),
        ];
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $categoryId = $request->input('category_id');
        $taxFilter = $request->input('tax_rate');

        $products = Product::query()
            ->with('category')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($taxFilter !== null && $taxFilter !== '', fn ($q) => $q->where('tax_rate', (float) $taxFilter))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $taxRates = Product::availableTaxRates();

        $allActive = Product::query()->get(['cost_price', 'sale_price', 'tax_rate']);
        $summary = [
            'total_products' => $allActive->count(),
            'general_19' => $allActive->filter(fn ($p) => (float) $p->tax_rate == 19.0)->count(),
            'differential_5' => $allActive->filter(fn ($p) => (float) $p->tax_rate == 5.0)->count(),
            'exempt_0' => $allActive->filter(fn ($p) => (float) $p->tax_rate == 0.0)->count(),
        ];

        return view('pricing.index', compact(
            'products',
            'categories',
            'taxRates',
            'summary',
            'search',
            'categoryId',
            'taxFilter'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sale_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $product->update([
            'sale_price' => $data['sale_price'],
            'tax_rate' => $data['tax_rate'],
        ]);

        return redirect()
            ->back()
            ->with('success', "Tarifa e IVA diferencial actualizados para «{$product->name}».");
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'rates' => ['required', 'array'],
            'rates.*.sale_price' => ['required', 'numeric', 'min:0'],
            'rates.*.tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        foreach ($data['rates'] as $productId => $values) {
            Product::where('id', $productId)->update([
                'sale_price' => $values['sale_price'],
                'tax_rate' => $values['tax_rate'],
            ]);
        }

        return redirect()
            ->route('pricing.index')
            ->with('success', 'Catálogo de tarifas y tasas de IVA diferencial actualizado masivamente.');
    }
}
