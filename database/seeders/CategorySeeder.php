<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Nutrición Canina Premium',
                'description' => 'Alimentos concentrados secos y húmedos para perros cachorros, adultos y senior.',
            ],
            [
                'id' => 2,
                'name' => 'Nutrición Felina Especializada',
                'description' => 'Concentrados super premium, sobres húmedos y fórmulas para gatos esterilizados.',
            ],
            [
                'id' => 3,
                'name' => 'Snacks y Premios Funcionales',
                'description' => 'Galletas horneadas, carnazas dentales, bocaditos liofilizados y premios de entrenamiento.',
            ],
            [
                'id' => 4,
                'name' => 'Paseo, Collares y Arneses',
                'description' => 'Arneses ergonómicos, traíllas retráctiles, collares reflectivos y placas de identificación.',
            ],
            [
                'id' => 5,
                'name' => 'Camas, Guacales y Descanso',
                'description' => 'Camas ortopédicas, cojines térmicos, bolsos viajeros y guacales homologados.',
            ],
            [
                'id' => 6,
                'name' => 'Juguetes e Interacción',
                'description' => 'Juguetes interactivos dispensadores, pelotas de caucho natural y mordedores resistentes.',
            ],
            [
                'id' => 7,
                'name' => 'Higiene, Baño y Estética',
                'description' => 'Champús dermocosméticos, acondicionadores, perfumes, cepillos y cortaúñas.',
            ],
            [
                'id' => 8,
                'name' => 'Cuidado Preventivo y Antipulgas',
                'description' => 'Pipetas spot-on, collares antipulgas y tabletas masticables de venta comercial.',
            ],
            [
                'id' => 9,
                'name' => 'Arenas Sanitarias y Rascadores',
                'description' => 'Arenas aglomerantes biodegradables, areneros cubiertos, gimnasios y rascadores de sisal.',
            ],
            [
                'id' => 10,
                'name' => 'Comederos y Dispensadores',
                'description' => 'Comederos antiansiedad, fuentes de agua automáticas y platos en acero inoxidable.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::withTrashed()->updateOrCreate(
                ['id' => $cat['id']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'active' => true,
                    'deleted_at' => null,
                ]
            );
        }
    }
}
