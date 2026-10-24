<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SystemModule extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'is_enabled',
        'is_core',
        'depends_on',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_core' => 'boolean',
        'depends_on' => 'array',
    ];

    public static function defaultModules(): array
    {
        return [
            [
                'key' => 'categories',
                'name' => 'Categorías de Catálogo',
                'description' => 'Clasificación de líneas de productos comerciales para PetShop.',
                'icon' => 'fa-solid fa-tags',
                'is_enabled' => true,
                'is_core' => false,
                'depends_on' => [],
            ],
            [
                'key' => 'products',
                'name' => 'Inventario y Lotes',
                'description' => 'Control de existencias en bodega, costos de adquisición, lotes, fechas de vencimiento y stock mínimo.',
                'icon' => 'fa-solid fa-boxes-stacked',
                'is_enabled' => true,
                'is_core' => false,
                'depends_on' => ['categories'],
            ],
            [
                'key' => 'pricing',
                'name' => 'Tarifas e IVA Diferencial',
                'description' => 'Asignación de precios comerciales de venta, margen de utilidad sobre costo e IVA diferencial (0%, 5%, 19%).',
                'icon' => 'fa-solid fa-file-invoice-dollar',
                'is_enabled' => true,
                'is_core' => false,
                'depends_on' => ['products'],
            ],
            [
                'key' => 'clients',
                'name' => 'Directorio de Clientes',
                'description' => 'Gestión de clientes compradores, documentos de identidad y múltiples teléfonos.',
                'icon' => 'fa-solid fa-users',
                'is_enabled' => true,
                'is_core' => false,
                'depends_on' => [],
            ],
            [
                'key' => 'sales',
                'name' => 'Ventas y Facturación POS',
                'description' => 'Facturación POS con IVA diferencial, control de lotes vencidos, descuento de inventario y soporte PDF.',
                'icon' => 'fa-solid fa-cash-register',
                'is_enabled' => true,
                'is_core' => false,
                'depends_on' => ['products', 'clients'],
            ],
            [
                'key' => 'providers',
                'name' => 'Proveedores y Distribuidores',
                'description' => 'Catálogo de proveedores mayoristas con NIT, contacto comercial y papelera.',
                'icon' => 'fa-solid fa-truck-field',
                'is_enabled' => true,
                'is_core' => false,
                'depends_on' => [],
            ],
            [
                'key' => 'purchases',
                'name' => 'Órdenes de Compra y Recepción',
                'description' => 'Abastecimiento a proveedores con recepción parcial o total e incremento transaccional de stock.',
                'icon' => 'fa-solid fa-cart-flatbed',
                'is_enabled' => true,
                'is_core' => false,
                'depends_on' => ['products', 'providers'],
            ],
            [
                'key' => 'reports',
                'name' => 'Reportes y Analítica Gerencial',
                'description' => 'Indicadores KPI en el Dashboard, Top 5 productos más vendidos y sugerencias de compra.',
                'icon' => 'fa-solid fa-chart-pie',
                'is_enabled' => true,
                'is_core' => false,
                'depends_on' => [],
            ],
        ];
    }

    public static function ensureSeeded(): void
    {
        try {
            if (! Schema::hasTable('system_modules')) {
                return;
            }

            if (self::query()->count() < count(self::defaultModules())) {
                foreach (self::defaultModules() as $module) {
                    self::query()->firstOrCreate(['key' => $module['key']], $module);
                }
                self::flushCache();
            }
        } catch (\Throwable) {
            // Ignore if DB not ready
        }
    }

    public static function enabledMap(): array
    {
        try {
            if (! Schema::hasTable('system_modules')) {
                return collect(self::defaultModules())->mapWithKeys(fn ($m) => [$m['key'] => true])->all();
            }

            self::ensureSeeded();

            return Cache::rememberForever('erp.system_modules_map', function () {
                return self::query()->pluck('is_enabled', 'key')->map(fn ($v) => (bool) $v)->all();
            });
        } catch (\Throwable) {
            return collect(self::defaultModules())->mapWithKeys(fn ($m) => [$m['key'] => true])->all();
        }
    }

    public static function isEnabled(string $key): bool
    {
        $map = self::enabledMap();

        return $map[$key] ?? true;
    }

    public static function flushCache(): void
    {
        Cache::forget('erp.system_modules_map');
    }
}
