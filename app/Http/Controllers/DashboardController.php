<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SystemModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de control con métricas, rol activo y accesos a módulos habilitados.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $roles = $user->getRoleNames();
        $activeRole = $user->activeRole()?->name;

        // 1. Tarjetas de Módulos (filtradas por permiso efectivo y por módulo habilitado)
        $modules = collect([
            [
                'title' => 'Inventario y Lotes',
                'description' => 'Existencias en bodega, costos de compra, lotes y vencimientos.',
                'route' => 'products.index',
                'permission' => 'ver-productos',
                'module' => 'products',
            ],
            [
                'title' => 'Tarifas e IVA',
                'description' => 'Asignación de precios de venta, margen de utilidad e IVA diferencial.',
                'route' => 'pricing.index',
                'permission' => 'ver-productos',
                'module' => 'pricing',
            ],
            [
                'title' => 'Categorías',
                'description' => 'Familias y líneas comerciales del catálogo.',
                'route' => 'categories.index',
                'permission' => 'ver-categorias',
                'module' => 'categories',
            ],
            [
                'title' => 'Clientes',
                'description' => 'Directorio de clientes y contactos telefónicos.',
                'route' => 'clients.index',
                'permission' => 'ver-clientes',
                'module' => 'clients',
            ],
            [
                'title' => 'Ventas POS',
                'description' => 'Facturación con IVA diferencial y control de lotes.',
                'route' => 'sales.index',
                'permission' => 'ver-ventas',
                'module' => 'sales',
            ],
            [
                'title' => 'Proveedores',
                'description' => 'Gestión de distribuidores y marcas mayoristas.',
                'route' => 'providers.index',
                'permission' => 'ver-proveedores',
                'module' => 'providers',
            ],
            [
                'title' => 'Compras',
                'description' => 'Órdenes de compra y recepción parcial o total.',
                'route' => 'purchases.index',
                'permission' => 'ver-compras',
                'module' => 'purchases',
            ],
            [
                'title' => 'Configuración',
                'description' => 'Seguridad, Identidad Corporativa y Gestión de Módulos.',
                'route' => 'settings.index',
                'permission' => 'ver-usuarios',
                'module' => null,
            ],
        ])->filter(function ($module) use ($user) {
            if ($module['module'] && ! SystemModule::isEnabled($module['module'])) {
                return false;
            }

            return $user->can($module['permission']);
        });

        // 2. KPIs Condicionados por Permisos y Módulo de Reportes
        $stats = collect();
        $reportsEnabled = SystemModule::isEnabled('reports');

        if ($reportsEnabled && ($user->can('ver-reportes') || ($user->can('ver-ventas') && $user->can('ver-compras')))) {
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

        if ($user->can('ver-ventas') && SystemModule::isEnabled('sales')) {
            $totalSales = Sale::where('status', '!=', 'anulada')
                ->whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->sum('total');

            $stats->push([
                'label' => 'Ventas del mes',
                'value' => '$' . number_format((float) $totalSales, 2, ',', '.'),
            ]);
        }

        if ($user->can('ver-compras') && SystemModule::isEnabled('purchases')) {
            $totalPurchases = Purchase::where('status', '!=', 'anulada')
                ->whereMonth('purchase_date', now()->month)
                ->whereYear('purchase_date', now()->year)
                ->sum('total');

            $stats->push([
                'label' => 'Compras del mes',
                'value' => '$' . number_format((float) $totalPurchases, 2, ',', '.'),
            ]);
        }

        if ($user->can('ver-productos') && SystemModule::isEnabled('products')) {
            $stats->push([
                'label' => 'Total productos activos',
                'value' => Product::where('active', true)->count(),
            ]);
        }

        // 3. Top 5 Productos Más Vendidos
        $topProducts = collect();
        if ($reportsEnabled && $user->can('ver-ventas') && SystemModule::isEnabled('sales')) {
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
        if ($user->can('ver-productos') && SystemModule::isEnabled('products')) {
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
