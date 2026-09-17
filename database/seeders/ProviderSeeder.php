<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provider;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        Provider::create(['nit' => '900123456-1', 'company_name' => 'Distribuidora Farmacéutica Vet S.A.S.', 'contact_person' => 'Pedro Ramírez', 'phone' => '3101234567']);
        Provider::create(['nit' => '900234567-2', 'company_name' => 'Nutrición Animal Ltda.', 'contact_person' => 'Laura Torres', 'phone' => '3112345678']);
        Provider::create(['nit' => '900345678-3', 'company_name' => 'Accesorios Pet Life', 'contact_person' => 'Jorge Castro', 'phone' => '3123456789']);
        Provider::create(['nit' => '900456789-4', 'company_name' => 'Laboratorios BioVet', 'contact_person' => 'Elena Morales', 'phone' => '3134567890']);
        Provider::create(['nit' => '900567890-5', 'company_name' => 'Insumos Médicos del Valle', 'contact_person' => 'Roberto Díaz', 'phone' => '3145678901']);
    }
}
