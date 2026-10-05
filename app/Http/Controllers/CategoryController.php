<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-categorias', only: ['index']),
            new Middleware('permission:crear-categorias', only: ['create', 'store']),
            new Middleware('permission:editar-categorias', only: ['edit', 'update']),
            new Middleware('permission:eliminar-categorias', only: ['destroy', 'restore']),
        ];
    }

    public function index(Request $request)
    {
        $showTrashed = $request->boolean('trashed') && $request->user()->can('eliminar-categorias');
        $categories = Category::query()
            ->withCount('products')
            ->when($showTrashed, fn($q) => $q->onlyTrashed())
            ->latest()
            ->paginate(10);

        return view('categories.index', compact('categories', 'showTrashed'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with(
                'error',
                "No se puede eliminar la categoría «{$category->name}»: contiene productos asociados."
            );
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría enviada a la papelera.');
    }

    public function restore(Category $category)
    {
        $category->restore();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría restaurada exitosamente.');
    }
}