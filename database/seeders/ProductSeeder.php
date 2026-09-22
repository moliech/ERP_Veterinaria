<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'barcode' => '770001',
                'name' => 'Amoxicilina 250mg Vet',
                'sale_price' => 28000,
                'current_stock' => 45,
                'minimum_stock' => 10
            ],
            [
                'category_id' => 1,
                'barcode' => '770002',
                'name' => 'Vacuna Pentavalente Canina',
                'sale_price' => 65000,
                'current_stock' => 30,
                'minimum_stock' => 5
            ],
            [
                'category_id' => 2,
                'barcode' => '770003',
                'name' => 'Chunky Adulto Pollo 15kg',
                'sale_price' => 125000,
                'current_stock' => 18,
                'minimum_stock' => 4
            ],
            [
                'category_id' => 2,
                'barcode' => '770004',
                'name' => 'Pro Plan Gato Sterilized 3kg',
                'sale_price' => 98000,
                'current_stock' => 12,
                'minimum_stock' => 3
            ],
            [
                'category_id' => 3,
                'barcode' => '770005',
                'name' => 'Arnés Táctico Canino Talla L',
                'sale_price' => 55000,
                'current_stock' => 20,
                'minimum_stock' => 5
            ],
            [
                'category_id' => 4,
                'barcode' => '770006',
                'name' => 'Champú Medicado de Avena 500ml',
                'sale_price' => 34000,
                'current_stock' => 25,
                'minimum_stock' => 8
            ],
            [
                'category_id' => 5,
                'barcode' => '770007',
                'name' => 'Consulta Médica General',
                'sale_price' => 50000,
                'current_stock' => 999,
                'minimum_stock' => 0
            ],
            [
                'category_id' => 6,
                'barcode' => '770008',
                'name' => 'NexGard Specta 10-25kg',
                'sale_price' => 72000,
                'current_stock' => 40,
                'minimum_stock' => 10
            ],
            [
                'category_id' => 7,
                'barcode' => '770009',
                'name' => 'Bocaditos de Hígado Deshidratado',
                'sale_price' => 15000,
                'current_stock' => 60,
                'minimum_stock' => 15
            ],
            [
                'category_id' => 9,
                'barcode' => '770010',
                'name' => 'Arena Sanitaria Aglomerante 10kg',
                'sale_price' => 42000,
                'current_stock' => 22,
                'minimum_stock' => 6
            ],
        ];

        foreach ($products as $prod) {
            Product::updateOrCreate(
                ['barcode' => $prod['barcode']],
                $prod
            );
        }
    }
}