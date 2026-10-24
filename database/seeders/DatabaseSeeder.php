<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            TestUserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProviderSeeder::class,
            OwnerSeeder::class,
            PetSeeder::class,
        ]);
    }
}
