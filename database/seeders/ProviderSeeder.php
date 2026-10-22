<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'nit' => '900123456-1',
                'company_name' => 'Laboratorios Zoetis Colombia SAS',
                'contact_person' => 'Dra. Andrea Rincón (Asesora Médica)',
                'phone' => '3157894512',
                'email' => 'ventas@zoetis.com.co',
                'address' => 'Zona Industrial Calle 26 # 68-35, Bogotá',
            ],
            [
                'nit' => '860987654-3',
                'company_name' => 'Distribuidora Veterinaria del Valle (VetValle)',
                'contact_person' => 'Carlos Alberto Medina',
                'phone' => '3124567890',
                'email' => 'pedidos@vetvalle.com',
                'address' => 'Av. Roosevelt # 34-12, Cali',
            ],
            [
                'nit' => '800543210-9',
                'company_name' => 'Nestlé Purina Pet Care Colombia',
                'contact_person' => 'Marcela Pineda (Canal Especializado)',
                'phone' => '3209876543',
                'email' => 'atencion.veterinaria@purina.com',
                'address' => 'Parque Empresarial Celta Bodega 45, Funza',
            ],
            [
                'nit' => '901234567-8',
                'company_name' => 'Insumos Médicos Quirúrgicos VetQuímica',
                'contact_person' => 'Ing. Roberto Silva',
                'phone' => '3187654321',
                'email' => 'contacto@vetquimica.com.co',
                'address' => 'Cra 50 # 100-20, Medellín',
            ],
            [
                'nit' => '830112233-4',
                'company_name' => 'Gabrica S.A.S. (Expertos en Mascotas)',
                'contact_person' => 'Felipe Restrepo',
                'phone' => '3104561122',
                'email' => 'pedidos@gabrica.co',
                'address' => 'Autopista Medellín Km 2.5 Parque Industrial Portos, Cota',
            ],
            [
                'nit' => '900456789-2',
                'company_name' => 'Virbac Colombia S.A.S.',
                'contact_person' => 'Dra. Claudia Ortiz',
                'phone' => '3178901234',
                'email' => 'servicio.cliente@virbac.com.co',
                'address' => 'Cra 7 # 156-68 Torre Norte Of. 804, Bogotá',
            ],
            [
                'nit' => '860002150-5',
                'company_name' => 'Boehringer Ingelheim Animal Health',
                'contact_person' => 'Dr. Fernando Castro',
                'phone' => '3112345678',
                'email' => 'salud.animal@boehringer.com',
                'address' => 'Cra 11 # 84-09 Piso 5, Bogotá',
            ],
            [
                'nit' => '901345678-0',
                'company_name' => 'Elanco Salud Animal Colombia',
                'contact_person' => 'Paola Gómez (Representante de Zona)',
                'phone' => '3145678901',
                'email' => 'contacto.co@elancoah.com',
                'address' => 'Calle 100 # 19-61 Piso 8, Bogotá',
            ],
            [
                'nit' => '860069284-7',
                'company_name' => 'Agrocampo S.A.S. - División Mayorista',
                'contact_person' => 'Jorge Hernán Duarte',
                'phone' => '3167890123',
                'email' => 'mayoristas@agrocampo.com.co',
                'address' => 'Calle 73 # 20-62, Bogotá',
            ],
            [
                'nit' => '900223344-6',
                'company_name' => 'Laboratorios Holliday-Scott Colombia',
                'contact_person' => 'Dra. Natalia Morales',
                'phone' => '3138901234',
                'email' => 'colombia@holliday-scott.com',
                'address' => 'Cra 43A # 1-50 San Fernando Plaza, Medellín',
            ],
            [
                'nit' => '800149696-2',
                'company_name' => 'Alimentos Polar Colombia (Dogourmet)',
                'contact_person' => 'Andrés Mauricio Mejía',
                'phone' => '3129012345',
                'email' => 'mascotas@empresas-polar.com',
                'address' => 'Km 19 Troncal de Occidente, Facatativá',
            ],
            [
                'nit' => '890302820-1',
                'company_name' => 'Carval de Colombia S.A.S.',
                'contact_person' => 'Dr. Gustavo Adolfo Valencia',
                'phone' => '3156789012',
                'email' => 'ventas@carval.com.co',
                'address' => 'Calle 29 Norte # 6A-40, Cali',
            ],
        ];

        foreach ($providers as $prov) {
            Provider::firstOrCreate(['nit' => $prov['nit']], $prov);
        }
    }
}
