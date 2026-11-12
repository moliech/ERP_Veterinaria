<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - VetPets ERP v1 (Clase 13)
|--------------------------------------------------------------------------
| Todas las rutas de interoperabilidad de la API REST residen bajo el prefijo /api/v1/...
| Versionar la API garantiza compatibilidad hacia adelante con clientes móviles y externos.
*/

Route::prefix('v1')->group(function () {

    // Pública: máximo 5 intentos de login por minuto por IP
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Protegidas con token Bearer de Sanctum: 60 peticiones por minuto
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show']);

        Route::get('sales/{sale}', [SaleController::class, 'show']);
        Route::post('sales', [SaleController::class, 'store']);
    });
});
