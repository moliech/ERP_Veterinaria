<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create(['category_id' => 1, 'barcode' => '77001', 'name' => 'Amoxicilina 250mg', 'sale_price' => 25000, 'current_stock' => 50, 'minimum_stock' => 10]);
        Product::create(['category_id' => 2, 'barcode' => '77002', 'name' => 'Alimento Canino Adulto 10kg', 'sale_price' => 110000, 'current_stock' => 20, 'minimum_stock' => 5]);
        Product::create(['category_id' => 3, 'barcode' => '77003', 'name' => 'Collar Antipulgas', 'sale_price' => 45000, 'current_stock' => 15, 'minimum_stock' => 3]);
        Product::create(['category_id' => 4, 'barcode' => '77004', 'name' => 'Champú Medicado 500ml', 'sale_price' => 32000, 'current_stock' => 30, 'minimum_stock' => 8]);
        Product::create(['category_id' => 5, 'barcode' => '77005', 'name' => 'Consulta Médica General', 'sale_price' => 50000, 'current_stock' => 999, 'minimum_stock' => 0]);
    }
}
