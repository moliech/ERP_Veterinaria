<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clientes / Propietarios
    Route::resource('clients', ClientController::class)->except(['show']);

    // Proveedores / Laboratorios
    Route::resource('providers', ProviderController::class);

    // Restauración (Papelera SoftDelete)
    Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
        ->withTrashed()->name('products.restore');
    Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])
        ->withTrashed()->name('categories.restore');

    // Catálogo de Productos y Categorías
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);

    // Ventas POS y Facturación PDF
    Route::resource('sales', SaleController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->withTrashed(['show']);
    Route::get('sales/{sale}/pdf', [SaleController::class, 'pdf'])
        ->withTrashed()
        ->name('sales.pdf');

    // Compras a Proveedores, Recepción e Impresión PDF
    Route::resource('purchases', PurchaseController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->withTrashed(['show']);
    Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])
        ->name('purchases.receive');
    Route::get('purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])
        ->withTrashed()
        ->name('purchases.pdf');
});

require __DIR__.'/auth.php';
