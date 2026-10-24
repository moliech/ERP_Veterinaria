<?php

use App\Http\Controllers\ActiveRoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rol activo (usuarios con varios roles)
    if (class_exists(ActiveRoleController::class)) {
        Route::get('rol-activo', [ActiveRoleController::class, 'edit'])->name('active-role.edit');
        Route::put('rol-activo', [ActiveRoleController::class, 'update'])->name('active-role.update');
    }

    // Productos y categorías
    Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
        ->withTrashed()->name('products.restore');
    Route::resource('products', ProductController::class)->except('show');

    Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])
        ->withTrashed()->name('categories.restore');
    Route::resource('categories', CategoryController::class)->except('show');

    // Clientes
    Route::patch('clients/{client}/restore', [ClientController::class, 'restore'])
        ->withTrashed()->name('clients.restore');
    Route::resource('clients', ClientController::class)->except('show');

    // Ventas
    Route::get('sales/{sale}/pdf', [SaleController::class, 'pdf'])->name('sales.pdf');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Proveedores (Clase 10)
    Route::patch('providers/{provider}/restore', [ProviderController::class, 'restore'])
        ->withTrashed()->name('providers.restore');
    Route::resource('providers', ProviderController::class)->except('show');

    // Compras (Clase 10)
    Route::patch('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])
        ->name('purchases.receive');
    Route::get('purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])
        ->name('purchases.pdf');
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Configuración: usuarios, roles y permisos (guía adicional de la Clase 10)
    Route::prefix('configuracion')->name('settings.')->group(function () {
        if (class_exists(SettingsController::class)) {
            Route::get('/', SettingsController::class)->name('index');
        } else {
            Route::get('/', fn () => redirect()->route('dashboard'))->name('index');
        }

        if (class_exists(UserController::class)) {
            Route::patch('usuarios/{user}/estado', [UserController::class, 'toggle'])
                ->name('users.toggle');
            Route::resource('usuarios', UserController::class)
                ->except(['show', 'destroy'])
                ->parameters(['usuarios' => 'user'])
                ->names('users');
        }

        if (class_exists(RoleController::class)) {
            Route::resource('roles', RoleController::class)->except('show');
        }

        if (class_exists(PermissionController::class)) {
            Route::get('permisos', [PermissionController::class, 'index'])->name('permissions.index');
            Route::put('permisos', [PermissionController::class, 'updateMatrix'])
                ->name('permissions.matrix');
            Route::post('permisos', [PermissionController::class, 'store'])->name('permissions.store');
            Route::delete('permisos/{permission}', [PermissionController::class, 'destroy'])
                ->name('permissions.destroy');
        }
    });
});

require __DIR__.'/auth.php';
