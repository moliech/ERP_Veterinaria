<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de control con métricas, rol activo y accesos a módulos.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $roles = $user->getRoleNames();
        $activeRole = $user->activeRole()?->name;

        // 1. Tarjetas de Módulos (filtradas por permiso)
        $modules = collect([
            [
                'title' => 'Productos',
                'description' => 'Catálogo de artículos, precios e inventario.',
                'route' => 'products.index',
                'permission' => 'ver-productos',
            ],
            [
                'title' => 'Categorías',
                'description' => 'Familias y clasificación de productos.',
                'route' => 'categories.index',
                'permission' => 'ver-categorias',
            ],
            [
                'title' => 'Clientes',
                'description' => 'Directorio de clientes y sus mascotas.',
                'route' => 'clients.index',
                'permission' => 'ver-clientes',
            ],
            [
                'title' => 'Ventas',
                'description' => 'Facturación POS y salida de mercancía.',
                'route' => 'sales.index',
                'permission' => 'ver-ventas',
            ],
            [
                'title' => 'Proveedores',
                'description' => 'Gestión de laboratorios y proveedores.',
                'route' => 'providers.index',
                'permission' => 'ver-proveedores',
            ],
            [
                'title' => 'Compras',
                'description' => 'Órdenes de compra y recepción de inventario.',
                'route' => 'purchases.index',
                'permission' => 'ver-compras',
            ],
            [
                'title' => 'Configuración',
                'description' => 'Usuarios, roles y permisos.',
                'route' => 'settings.index',
                'permission' => 'ver-usuarios',
            ],
        ])->filter(fn ($module) => $user->can($module['permission']));

        // 2. KPIs Condicionados por Permisos
        $stats = collect();

        // KPI: Ventas − Compras del mes (permiso directo ver-reportes o ambos ver-ventas y ver-compras)
        if ($user->can('ver-reportes') || ($user->can('ver-ventas') && $user->can('ver-compras'))) {
            $salesTotal = Sale::where('status', '!=', 'anulada')
                ->whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->sum('total');

            $purchasesTotal = Purchase::where('status', '!=', 'anulada')
                ->whereMonth('purchase_date', now()->month)
                ->whereYear('purchase_date', now()->year)
                ->sum('total');

            $balance = (float) ($salesTotal - $purchasesTotal);

            $stats->push([
                'label' => 'Ventas − Compras del mes',
                'value' => ($balance >= 0 ? '+' : '-') . '$' . number_format(abs($balance), 2, ',', '.'),
            ]);
        }

        if ($user->can('ver-ventas')) {
            $totalSales = Sale::where('status', '!=', 'anulada')
                ->whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->sum('total');

            $stats->push([
                'label' => 'Ventas del mes',
                'value' => '$' . number_format((float) $totalSales, 2, ',', '.'),
            ]);
        }

        if ($user->can('ver-compras')) {
            $totalPurchases = Purchase::where('status', '!=', 'anulada')
                ->whereMonth('purchase_date', now()->month)
                ->whereYear('purchase_date', now()->year)
                ->sum('total');

            $stats->push([
                'label' => 'Compras del mes',
                'value' => '$' . number_format((float) $totalPurchases, 2, ',', '.'),
            ]);
        }

        if ($user->can('ver-productos')) {
            $stats->push([
                'label' => 'Total productos',
                'value' => Product::where('active', true)->count(),
            ]);
        }

        // 3. Top 5 Productos Más Vendidos
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

        // 4. Productos para Reabastecimiento (stock <= 5 o stock <= mínimo)
        $lowStock = collect();
        if ($user->can('ver-productos')) {
            $lowStock = Product::where('active', true)
                ->where(function ($q) {
                    $q->where('current_stock', '<=', 5)
                      ->orWhereColumn('current_stock', '<=', 'minimum_stock');
                })
                ->orderBy('current_stock')
                ->limit(8)
                ->get();
        }

        return view('dashboard', [
            'roles' => $roles,
            'activeRole' => $activeRole,
            'stats' => $stats,
            'topProducts' => $topProducts,
            'lowStock' => $lowStock,
            'modules' => $modules,
        ]);
    }
}
