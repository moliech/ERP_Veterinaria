<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'barcode' => '770001',
                'name' => 'Taste of the Wild High Prairie Adulto 12.2kg',
                'cost_price' => 185000,
                'sale_price' => 245000,
                'tax_rate' => 5.00,
                'batch_code' => 'LOT-2026-TW01',
                'expiration_date' => now()->addMonths(8)->toDateString(),
                'current_stock' => 24,
                'minimum_stock' => 6,
            ],
            [
                'category_id' => 1,
                'barcode' => '770002',
                'name' => 'Chunky Adulto Pollo Razas Medianas 15kg',
                'cost_price' => 96000,
                'sale_price' => 128000,
                'tax_rate' => 5.00,
                'batch_code' => 'LOT-2026-CH14',
                'expiration_date' => now()->addMonths(6)->toDateString(),
                'current_stock' => 18,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => 2,
                'barcode' => '770003',
                'name' => 'Pro Plan Gato Sterilized Salmón 3kg',
                'cost_price' => 72000,
                'sale_price' => 98000,
                'tax_rate' => 5.00,
                'batch_code' => 'LOT-2026-PP09',
                'expiration_date' => now()->addMonths(10)->toDateString(),
                'current_stock' => 15,
                'minimum_stock' => 4,
            ],
            [
                'category_id' => 2,
                'barcode' => '770004',
                'name' => 'Royal Canin Instinctive Sobres Húmedos Pack x12',
                'cost_price' => 62000,
                'sale_price' => 84000,
                'tax_rate' => 5.00,
                'batch_code' => 'LOT-2026-RC22',
                'expiration_date' => now()->addDays(22)->toDateString(), // Próximo a vencer (<30 días)
                'current_stock' => 4, // Stock bajo para demostrar alerta de reabastecimiento
                'minimum_stock' => 6,
            ],
            [
                'category_id' => 3,
                'barcode' => '770005',
                'name' => 'Bocaditos de Hígado Liofilizado Premium 150g',
                'cost_price' => 14500,
                'sale_price' => 22000,
                'tax_rate' => 5.00,
                'batch_code' => 'LOT-2026-SN05',
                'expiration_date' => now()->addMonths(5)->toDateString(),
                'current_stock' => 45,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => 4,
                'barcode' => '770006',
                'name' => 'Arnés Ergonómico Reflectivo Talla L',
                'cost_price' => 36000,
                'sale_price' => 58000,
                'tax_rate' => 19.00,
                'batch_code' => 'SER-ACC-104',
                'expiration_date' => null, // No perecedero
                'current_stock' => 20,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => 5,
                'barcode' => '770007',
                'name' => 'Cama Ortopédica Impermeable Antidesgarro XL',
                'cost_price' => 95000,
                'sale_price' => 145000,
                'tax_rate' => 19.00,
                'batch_code' => 'SER-CAM-208',
                'expiration_date' => null, // No perecedero
                'current_stock' => 3, // Stock bajo para demostrar alerta de reabastecimiento
                'minimum_stock' => 5,
            ],
            [
                'category_id' => 6,
                'barcode' => '770008',
                'name' => 'Juguete Interactivo Kong Classic Caucho Natural M',
                'cost_price' => 31000,
                'sale_price' => 49000,
                'tax_rate' => 19.00,
                'batch_code' => 'SER-JUG-312',
                'expiration_date' => null, // No perecedero
                'current_stock' => 32,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => 7,
                'barcode' => '770009',
                'name' => 'Champú Dermocosmético de Avena y Aloe 500ml',
                'cost_price' => 22000,
                'sale_price' => 36000,
                'tax_rate' => 19.00,
                'batch_code' => 'LOT-2026-CHM8',
                'expiration_date' => now()->addMonths(14)->toDateString(),
                'current_stock' => 28,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => 8,
                'barcode' => '770010',
                'name' => 'NexGard Spectra Tableta Masticable 15-30kg',
                'cost_price' => 54000,
                'sale_price' => 76000,
                'tax_rate' => 0.00, // Exento (0% IVA medicamento/antiparasitario)
                'batch_code' => 'LOT-2026-NX41',
                'expiration_date' => now()->addMonths(12)->toDateString(),
                'current_stock' => 35,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => 9,
                'barcode' => '770011',
                'name' => 'Arena Sanitaria Aglomerante Aroma Lavanda 10kg',
                'cost_price' => 28000,
                'sale_price' => 44000,
                'tax_rate' => 19.00,
                'batch_code' => 'LOT-2026-AR19',
                'expiration_date' => null, // No perecedero
                'current_stock' => 26,
                'minimum_stock' => 6,
            ],
            [
                'category_id' => 10,
                'barcode' => '770012',
                'name' => 'Fuente Bebedero Automática con Filtro Carbón 2.5L',
                'cost_price' => 74000,
                'sale_price' => 115000,
                'tax_rate' => 19.00,
                'batch_code' => 'SER-BEB-501',
                'expiration_date' => null, // No perecedero
                'current_stock' => 12,
                'minimum_stock' => 4,
            ],
        ];

        foreach ($products as $prod) {
            Product::withTrashed()->updateOrCreate(
                ['barcode' => $prod['barcode']],
                array_merge($prod, ['active' => true, 'deleted_at' => null])
            );
        }
    }
}
