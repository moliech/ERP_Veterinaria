<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@vetpets.com'],
            [
                'name' => 'Administrador VetPets',
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'esteban@vetpets.com'],
            [
                'name' => 'Esteban Usuario',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
