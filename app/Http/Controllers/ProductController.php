<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-productos', only: ['index']),
            new Middleware('permission:crear-productos', only: ['create', 'store']),
            new Middleware('permission:editar-productos', only: ['edit', 'update']),
            new Middleware('permission:eliminar-productos', only: ['destroy', 'restore']),
        ];
    }

    public function index(Request $request)
    {   
        $showTrashed=$request->boolean('trashed')&&$request->user()->can('eliminar-productos');
        $products = Product::query()
            ->with('category')
            ->when($showTrashed,fn($q)=>$q->onlyTrashed())
            ->latest()
            ->paginate(10);

        return view('products.index', compact('products', 'showTrashed'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated());
        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto enviado a la papelera.');
    }

    public function restore(Product $product)
    {
        $product->restore();
        return redirect()->route('products.index')->with('success', 'Producto restaurado correctamente.');
    }
}