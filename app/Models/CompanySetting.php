<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'business_subtitle',
        'nit',
        'address',
        'phone',
        'email',
        'tax_rate',
        'currency',
        'invoice_prefix',
        'purchase_prefix',
        'primary_color',
        'icon_class',
        'invoice_footer',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
    ];

    public static function defaultAttributes(): array
    {
        return [
            'company_name' => 'VetPets Comercial',
            'business_subtitle' => 'PetShop & Suministros',
            'nit' => '901.452.890-1',
            'address' => 'Calle 25 # 14-32, Cartago, Valle del Cauca',
            'phone' => '(602) 211-4500 · 315 890 1234',
            'email' => 'facturacion@vetpets.co',
            'tax_rate' => 19.00,
            'currency' => 'COP',
            'invoice_prefix' => 'FAC',
            'purchase_prefix' => 'OC',
            'primary_color' => 'emerald',
            'icon_class' => 'fa-solid fa-paw',
            'invoice_footer' => 'Gracias por su compra. Régimen Común · Facturación POS y Abastecimiento Comercial.',
        ];
    }

    public static function current(): self
    {
        try {
            if (! Schema::hasTable('company_settings')) {
                return new self(self::defaultAttributes());
            }

            $attributes = Cache::rememberForever('erp.company_settings_data', function () {
                $record = self::query()->first() ?? self::query()->create(self::defaultAttributes());

                return $record->attributesToArray();
            });

            if (! is_array($attributes)) {
                self::flushCache();
                $record = self::query()->first() ?? self::query()->create(self::defaultAttributes());
                $attributes = $record->attributesToArray();
            }

            $instance = (new self())->forceFill($attributes);
            $instance->exists = isset($attributes['id']);

            return $instance;
        } catch (\Throwable) {
            return new self(self::defaultAttributes());
        }
    }

    public static function flushCache(): void
    {
        Cache::forget('erp.company_settings');
        Cache::forget('erp.company_settings_data');
    }

    public static function availableColors(): array
    {
        return [
            'emerald' => [
                'label' => 'Esmeralda Corporativo',
                'hex' => '#059669',
                '50' => '#ecfdf5', '100' => '#d1fae5', '200' => '#a7f3d0', '300' => '#6ee7b7',
                '400' => '#34d399', '500' => '#10b981', '600' => '#059669', '700' => '#047857', '800' => '#065f46', '900' => '#064e3b',
            ],
            'blue' => [
                'label' => 'Azul Empresarial',
                'hex' => '#2563eb',
                '50' => '#eff6ff', '100' => '#dbeafe', '200' => '#bfdbfe', '300' => '#93c5fd',
                '400' => '#60a5fa', '500' => '#3b82f6', '600' => '#2563eb', '700' => '#1d4ed8', '800' => '#1e40af', '900' => '#1e3a8a',
            ],
            'indigo' => [
                'label' => 'Índigo Ejecutivo',
                'hex' => '#4f46e5',
                '50' => '#eef2ff', '100' => '#e0e7ff', '200' => '#c7d2fe', '300' => '#a5b4fc',
                '400' => '#818cf8', '500' => '#6366f1', '600' => '#4f46e5', '700' => '#4338ca', '800' => '#3730a3', '900' => '#312e81',
            ],
            'teal' => [
                'label' => 'Turquesa Comercial',
                'hex' => '#0d9488',
                '50' => '#f0fdfa', '100' => '#ccfbf1', '200' => '#99f6e4', '300' => '#5eead4',
                '400' => '#2dd4bf', '500' => '#14b8a6', '600' => '#0d9488', '700' => '#0f766e', '800' => '#115e59', '900' => '#134e4a',
            ],
            'violet' => [
                'label' => 'Violeta Moderno',
                'hex' => '#7c3aed',
                '50' => '#f5f3ff', '100' => '#ede9fe', '200' => '#ddd6fe', '300' => '#c4b5fd',
                '400' => '#a78bfa', '500' => '#8b5cf6', '600' => '#7c3aed', '700' => '#6d28d9', '800' => '#5b21b6', '900' => '#4c1d95',
            ],
            'amber' => [
                'label' => 'Ámbar Retail',
                'hex' => '#d97706',
                '50' => '#fffbeb', '100' => '#fef3c7', '200' => '#fde68a', '300' => '#fcd34d',
                '400' => '#fbbf24', '500' => '#f59e0b', '600' => '#d97706', '700' => '#b45309', '800' => '#92400e', '900' => '#78350f',
            ],
            'rose' => [
                'label' => 'Coral / Rubí',
                'hex' => '#e11d48',
                '50' => '#fff1f2', '100' => '#ffe4e6', '200' => '#fecdd3', '300' => '#fda4af',
                '400' => '#fb7185', '500' => '#f43f5e', '600' => '#e11d48', '700' => '#be123c', '800' => '#9f1239', '900' => '#881337',
            ],
        ];
    }

    public static function availableIcons(): array
    {
        return [
            'fa-solid fa-paw' => 'Huella PetShop (fa-paw)',
            'fa-solid fa-store' => 'Tienda Retail (fa-store)',
            'fa-solid fa-bag-shopping' => 'Bolsa Comercial (fa-bag-shopping)',
            'fa-solid fa-building' => 'Corporativo (fa-building)',
            'fa-solid fa-boxes-stacked' => 'Centro Logístico (fa-boxes-stacked)',
            'fa-solid fa-shield-dog' => 'Cuidado Mascotas (fa-shield-dog)',
        ];
    }

    public function colorMeta(): array
    {
        $colors = self::availableColors();

        return $colors[$this->primary_color] ?? $colors['emerald'];
    }

    public function primaryColorHex(): string
    {
        return $this->colorMeta()['hex'];
    }
}
