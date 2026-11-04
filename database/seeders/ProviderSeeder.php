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
                'company_name' => 'Distribuidora Nacional PetCare S.A.S.',
                'contact_person' => 'Andrea Rincón (Ejecutiva Comercial)',
                'phone' => '3157894512',
                'email' => 'ventas@petcarecolombia.com.co',
                'address' => 'Zona Industrial Calle 26 # 68-35, Bogotá',
            ],
            [
                'nit' => '860987654-3',
                'company_name' => 'Comercializadora Mascotas del Valle (PetValle)',
                'contact_person' => 'Carlos Alberto Medina',
                'phone' => '3124567890',
                'email' => 'pedidos@petvalle.com',
                'address' => 'Av. Roosevelt # 34-12, Cali',
            ],
            [
                'nit' => '800543210-9',
                'company_name' => 'Nestlé Purina Pet Care Colombia',
                'contact_person' => 'Marcela Pineda (Canal Especializado)',
                'phone' => '3209876543',
                'email' => 'canal.petshop@purina.com',
                'address' => 'Parque Empresarial Celta Bodega 45, Funza',
            ],
            [
                'nit' => '901234567-8',
                'company_name' => 'Importadora de Accesorios K9 & Felinos S.A.S.',
                'contact_person' => 'Roberto Silva',
                'phone' => '3187654321',
                'email' => 'contacto@k9accesorios.com.co',
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
                'company_name' => 'Royal Canin Colombia S.A.S.',
                'contact_person' => 'Claudia Ortiz',
                'phone' => '3178901234',
                'email' => 'servicio.cliente@royalcanin.com.co',
                'address' => 'Cra 7 # 156-68 Torre Norte Of. 804, Bogotá',
            ],
            [
                'nit' => '860002150-5',
                'company_name' => 'italcol Mascotas S.A. (Línea Chunky & Mirringo)',
                'contact_person' => 'Fernando Castro',
                'phone' => '3112345678',
                'email' => 'pedidos.mascotas@italcol.com',
                'address' => 'Km 13 Vía Girón, Bucaramanga',
            ],
            [
                'nit' => '901345678-0',
                'company_name' => 'Elanco Pet Health Colombia',
                'contact_person' => 'Paola Gómez (Representante Regional)',
                'phone' => '3145678901',
                'email' => 'contacto.co@elancoah.com',
                'address' => 'Calle 100 # 19-61 Piso 8, Bogotá',
            ],
            [
                'nit' => '860069284-7',
                'company_name' => 'Agrocampo S.A.S. - División Mayorista PetShop',
                'contact_person' => 'Jorge Hernán Duarte',
                'phone' => '3167890123',
                'email' => 'mayoristas@agrocampo.com.co',
                'address' => 'Calle 73 # 20-62, Bogotá',
            ],
            [
                'nit' => '900223344-6',
                'company_name' => 'BioArenas y Sanitarios Felinos de Colombia',
                'contact_person' => 'Natalia Morales',
                'phone' => '3138901234',
                'email' => 'ventas@bioarenas.com.co',
                'address' => 'Cra 43A # 1-50 San Fernando Plaza, Medellín',
            ],
            [
                'nit' => '800149696-2',
                'company_name' => 'Alimentos Polar Colombia (Línea Dogourmet)',
                'contact_person' => 'Andrés Mauricio Mejía',
                'phone' => '3129012345',
                'email' => 'mascotas@empresas-polar.com',
                'address' => 'Km 19 Troncal de Occidente, Facatativá',
            ],
            [
                'nit' => '890302820-1',
                'company_name' => 'Distribuidora SurtiMascotas de Occidente S.A.S.',
                'contact_person' => 'Gustavo Adolfo Valencia',
                'phone' => '3156789012',
                'email' => 'ventas@surtimascotas.com.co',
                'address' => 'Calle 29 Norte # 6A-40, Cali',
            ],
        ];

        foreach ($providers as $prov) {
            Provider::withTrashed()->updateOrCreate(
                ['nit' => $prov['nit']],
                array_merge($prov, ['deleted_at' => null])
            );
        }
    }
}
