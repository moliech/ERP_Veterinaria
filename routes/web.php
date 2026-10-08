<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Restauración (Papelera SoftDelete)
    Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
        ->withTrashed()->name('products.restore');
    Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])
        ->withTrashed()->name('categories.restore');

    // Rutas de Recurso CRUD
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
});

require __DIR__.'/auth.php';
