<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\OwnerPhone;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['document_type' => 'CC', 'document_number' => '1112789451', 'first_name' => 'Carlos Andrés', 'last_name' => 'Pérez Giraldo', 'address' => 'Calle 10 # 5-20, Barrio El Prado, Cartago', 'email' => 'carlos.perez@correo.co', 'phone' => '3001112233'],
            ['document_type' => 'CC', 'document_number' => '1112789452', 'first_name' => 'María Fernanda', 'last_name' => 'Gómez Salazar', 'address' => 'Carrera 4 # 12-40, Centro, Cartago', 'email' => 'maria.gomez@correo.co', 'phone' => '3002223344'],
            ['document_type' => 'CC', 'document_number' => '1112789453', 'first_name' => 'Juan Sebastián', 'last_name' => 'Rodríguez Mejía', 'address' => 'Av. del Río # 8-15, Cartago', 'email' => 'juan.rodriguez@correo.co', 'phone' => '3003334455'],
            ['document_type' => 'CC', 'document_number' => '1112789454', 'first_name' => 'Ana Lucía', 'last_name' => 'Martínez Ospina', 'address' => 'Calle 15 # 2-10, Santa Ana, Cartago', 'email' => 'ana.martinez@correo.co', 'phone' => '3004445566'],
            ['document_type' => 'CC', 'document_number' => '1112789455', 'first_name' => 'Luis Eduardo', 'last_name' => 'López Cardona', 'address' => 'Transversal 5 # 9-30, San Jerónimo', 'email' => 'luis.lopez@correo.co', 'phone' => '3005556677'],
            ['document_type' => 'CC', 'document_number' => '1112789456', 'first_name' => 'Diana Carolina', 'last_name' => 'Restrepo Echeverri', 'address' => 'Carrera 7 # 18-45, Los Alpes, Cartago', 'email' => 'diana.restrepo@correo.co', 'phone' => '3156667788'],
            ['document_type' => 'CC', 'document_number' => '1112789457', 'first_name' => 'Jorge Eliécer', 'last_name' => 'Castaño Valencia', 'address' => 'Calle 22 # 6-14, Guadalupe, Cartago', 'email' => 'jorge.castano@correo.co', 'phone' => '3167778899'],
            ['document_type' => 'CC', 'document_number' => '1112789458', 'first_name' => 'Paola Andrea', 'last_name' => 'Bedoya Quintero', 'address' => 'Carrera 9 # 11-50, La Isleta, Cartago', 'email' => 'paola.bedoya@correo.co', 'phone' => '3178889900'],
            ['document_type' => 'CE', 'document_number' => '700891234', 'first_name' => 'Alejandro', 'last_name' => 'Mendoza Ríos', 'address' => 'Condominio Campestre Palma Real Casa 14', 'email' => 'alejandro.mendoza@correo.co', 'phone' => '3189990011'],
            ['document_type' => 'CC', 'document_number' => '1112789460', 'first_name' => 'Valentina', 'last_name' => 'Zuluga Henao', 'address' => 'Calle 14 # 4-88, El Carmen, Cartago', 'email' => 'valentina.zuluaga@correo.co', 'phone' => '3190001122'],
        ];

        foreach ($clients as $c) {
            $phone = $c['phone'];
            unset($c['phone']);

            $owner = Owner::updateOrCreate(
                ['document_number' => $c['document_number']],
                $c
            );

            OwnerPhone::firstOrCreate(
                ['owner_id' => $owner->id, 'phone_number' => $phone],
                ['phone_type' => 'Móvil']
            );
        }
    }
}
