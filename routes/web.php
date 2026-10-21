<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('clients', ClientController::class)->except(['show']);
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
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('sales/{sale}/pdf', [SaleController::class, 'pdf'])
    ->name('sales.pdf');
});

require __DIR__.'/auth.php';
