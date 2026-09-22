<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Farmacia Veterinaria',
                'description' => 'Medicamentos, antibióticos y vacunas'
            ],
            [
                'name' => 'Alimentos y Nutrición',
                'description' => 'Concentrados, latas y suplementos'
            ],
            [
                'name' => 'Accesorios y Juguetes',
                'description' => 'Collares, correas, camas y juguetes'
            ],
            [
                'name' => 'Higiene y Estética',
                'description' => 'Champú, cepillos, colonias y cortaúñas'
            ],
            [
                'name' => 'Servicios Médicos',
                'description' => 'Consultas, cirugías, ecografías y laboratorio'
            ],
            [
                'name' => 'Desparasitantes y Antipulgas',
                'description' => 'Pipetas, pastillas y collares medicados'
            ],
            [
                'name' => 'Snacks y Premios',
                'description' => 'Galletas, carnazas y bocadillos de entrenamiento'
            ],
            [
                'name' => 'Ropa y Moda Canina',
                'description' => 'Capotas, impermeables y chalecos'
            ],
            [
                'name' => 'Arena y Muebles para Gatos',
                'description' => 'Rascadores, gateras y arenas sanitarias'
            ],
            [
                'name' => 'Especialidades Exóticas',
                'description' => 'Insumos para aves, roedores y reptiles'
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }
    }
}