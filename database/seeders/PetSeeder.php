<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pet;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        Pet::create(['owner_id' => 1, 'name' => 'Max', 'species' => 'Canino', 'breed' => 'Labrador', 'birth_date' => '2021-05-10', 'weight' => 25.5]);
        Pet::create(['owner_id' => 1, 'name' => 'Felix', 'species' => 'Felino', 'breed' => 'Siames', 'birth_date' => '2022-01-15', 'weight' => 4.2]);
        Pet::create(['owner_id' => 2, 'name' => 'Rocky', 'species' => 'Canino', 'breed' => 'Bulldog', 'birth_date' => '2020-11-20', 'weight' => 18.0]);
        Pet::create(['owner_id' => 3, 'name' => 'Luna', 'species' => 'Felino', 'breed' => 'Persa', 'birth_date' => '2023-03-05', 'weight' => 3.8]);
        Pet::create(['owner_id' => 4, 'name' => 'Toby', 'species' => 'Canino', 'breed' => 'Poodle', 'birth_date' => '2019-08-12', 'weight' => 8.5]);
    }
}
