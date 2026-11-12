<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SaleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-ventas', only: ['show']),
            new Middleware('permission:crear-ventas', only: ['store']),
        ];
    }

    /**
     * GET /api/v1/sales/{sale}
     */
    public function show(Sale $sale)
    {
        return new SaleResource($sale->load(['client', 'user', 'details.product']));
    }

    /**
     * POST /api/v1/sales
     * Reutiliza la validación (StoreSaleRequest) y la lógica transaccional de SaleService.
     */
    public function store(StoreSaleRequest $request, SaleService $service)
    {
        $sale = $service->register($request->validated(), $request->user()->id);

        return (new SaleResource($sale->load(['client', 'user', 'details.product'])))
            ->response()
            ->setStatusCode(201);
    }
}
