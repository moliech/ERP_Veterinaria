<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Owner;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de control con KPIs calculados dinámicamente y el Top 5 de ventas.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $roles = $user->getRoleNames();

        // 1. KPIs del Negocio (Adaptados a VetPets ERP y condicionados por roles/permisos)
        $stats = collect();

        if ($user->can('ver-productos')) {
            $stats->push([
                'label'       => 'Productos Activos',
                'value'       => Product::where('active', true)->count(),
                'icon'        => 'fa-boxes-stacked',
                'color'       => 'emerald',
                'badge'       => 'En catálogo',
            ]);

            $stats->push([
                'label'       => 'Alertas Stock Bajo (≤ Mínimo)',
                'value'       => Product::where('active', true)->whereColumn('current_stock', '<=', 'minimum_stock')->count(),
                'icon'        => 'fa-triangle-exclamation',
                'color'       => 'rose',
                'badge'       => 'Reorden sugerido',
            ]);
        }

        if ($user->can('ver-clientes')) {
            $stats->push([
                'label'       => 'Clientes / Propietarios',
                'value'       => Owner::count(),
                'icon'        => 'fa-users',
                'color'       => 'blue',
                'badge'       => 'Registrados',
            ]);
        }

        if ($user->can('ver-ventas')) {
            $salesMonth = Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->where('status', '!=', 'anulada');

            $stats->push([
                'label'       => 'Facturación del Mes',
                'value'       => '$' . number_format((float) $salesMonth->sum('total'), 2),
                'icon'        => 'fa-sack-dollar',
                'color'       => 'amber',
                'badge'       => $salesMonth->count() . ' transacciones',
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

        return view('dashboard', compact('user', 'roles', 'stats', 'topProducts'));
    }
}
