<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-productos'),
        ];
    }

    /**
     * GET /api/v1/products
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('category')
            ->where('active', true)
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->orderBy('name')
            ->paginate(min($request->integer('per_page', 15), 100));

        return ProductResource::collection($products);
    }

    /**
     * GET /api/v1/products/{product}
     */
    public function show(Product $product)
    {
        return new ProductResource($product->load('category'));
    }
}
