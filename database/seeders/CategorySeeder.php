<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Farmacia Veterinaria', 'description' => 'Medicamentos y antibióticos']);
        Category::create(['name' => 'Alimentos y Nutrición', 'description' => 'Concentrados y galletas']);
        Category::create(['name' => 'Accesorios y Juguetes', 'description' => 'Collares, correas y juguetes']);
        Category::create(['name' => 'Higiene y Estética', 'description' => 'Champú, jabones y cepillos']);
        Category::create(['name' => 'Servicios Médicos', 'description' => 'Consultas, cirugías y vacunas']);
    }
}
