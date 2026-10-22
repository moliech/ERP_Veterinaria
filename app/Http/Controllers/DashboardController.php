<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Owner;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Purchase;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de control con KPIs calculados dinámicamente, Top 5 de ventas y Alerta de Reabastecimiento.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $roles = $user->getRoleNames();

        // 1. KPIs del Negocio (Adaptados a VetPets ERP y condicionados por roles/permisos)
        $stats = collect();

        $totalSalesMonth = 0;
        $totalPurchasesMonth = 0;

        if ($user->can('ver-productos')) {
            $stats->push([
                'label' => 'Productos Activos',
                'value' => Product::where('active', true)->count(),
                'icon'  => 'fa-boxes-stacked',
                'color' => 'emerald',
                'badge' => 'En catálogo activo',
            ]);

            $lowStockCount = Product::where('active', true)->whereColumn('current_stock', '<=', 'minimum_stock')->count();
            $stats->push([
                'label' => 'Alertas Stock Bajo',
                'value' => $lowStockCount,
                'icon'  => 'fa-triangle-exclamation',
                'color' => $lowStockCount > 0 ? 'rose' : 'emerald',
                'badge' => $lowStockCount > 0 ? 'Reorden requerido' : 'Inventario óptimo',
            ]);
        }

        if ($user->can('ver-clientes')) {
            $stats->push([
                'label' => 'Clientes / Mascotas',
                'value' => Owner::count(),
                'icon'  => 'fa-users',
                'color' => 'blue',
                'badge' => 'Clientes registrados',
            ]);
        }

        if ($user->can('ver-ventas')) {
            $salesMonthQuery = Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->where('status', '!=', 'anulada');

            $totalSalesMonth = (float) $salesMonthQuery->sum('total');

            $stats->push([
                'label' => 'Ventas del Mes',
                'value' => '$' . number_format($totalSalesMonth, 2, ',', '.'),
                'icon'  => 'fa-sack-dollar',
                'color' => 'amber',
                'badge' => $salesMonthQuery->count() . ' facturas POS',
            ]);
        }

        if ($user->can('ver-compras')) {
            $purchasesMonthQuery = Purchase::whereMonth('purchase_date', now()->month)
                ->whereYear('purchase_date', now()->year)
                ->where('status', '!=', 'anulada');

            $totalPurchasesMonth = (float) $purchasesMonthQuery->sum('total');

            $stats->push([
                'label' => 'Compras del Mes',
                'value' => '$' . number_format($totalPurchasesMonth, 2, ',', '.'),
                'icon'  => 'fa-receipt',
                'color' => 'purple',
                'badge' => $purchasesMonthQuery->count() . ' órdenes procesadas',
            ]);

            $pendingPurchases = Purchase::whereIn('status', ['pendiente', 'parcial'])->count();
            $stats->push([
                'label' => 'Órdenes por Recibir',
                'value' => $pendingPurchases,
                'icon'  => 'fa-truck-ramp-box',
                'color' => 'indigo',
                'badge' => 'Pendientes en bodega',
            ]);
        }

        if ($user->can('ver-proveedores')) {
            $stats->push([
                'label' => 'Proveedores Activos',
                'value' => Provider::count(),
                'icon'  => 'fa-building',
                'color' => 'teal',
                'badge' => 'Laboratorios y marcas',
            ]);
        }

        if ($user->can('ver-ventas') && $user->can('ver-compras')) {
            $balance = $totalSalesMonth - $totalPurchasesMonth;
            $stats->push([
                'label' => 'Balance Operativo',
                'value' => ($balance >= 0 ? '+' : '-') . '$' . number_format(abs($balance), 2, ',', '.'),
                'icon'  => 'fa-scale-balanced',
                'color' => $balance >= 0 ? 'emerald' : 'rose',
                'badge' => 'Ventas vs Compras',
            ]);
        }

        // 2. Top 5 Productos Más Vendidos del Mes Actual
        $topProducts = collect();
        if ($user->can('ver-ventas')) {
            $topProducts = SaleDetail::query()
                ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
                ->whereHas('sale', function ($q) {
                    $q->whereMonth('sale_date', now()->month)
                      ->whereYear('sale_date', now()->year)
                      ->where('status', '!=', 'anulada');
                })
                ->with('product:id,name,sale_price')
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
        }

        // 3. Productos Críticos para Reabastecimiento Inmediato (Stock <= Mínimo)
        $criticalStockProducts = collect();
        if ($user->can('ver-productos')) {
            $criticalStockProducts = Product::where('active', true)
                ->whereColumn('current_stock', '<=', 'minimum_stock')
                ->with('category')
                ->orderBy('current_stock')
                ->limit(8)
                ->get();
        }

        return view('dashboard', compact('user', 'roles', 'stats', 'topProducts', 'criticalStockProducts'));
    }
}
