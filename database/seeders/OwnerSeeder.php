<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Owner;
use App\Models\OwnerPhone;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        $o1 = Owner::create(['document_type' => 'CC', 'document_number' => '10101', 'first_name' => 'Carlos', 'last_name' => 'Pérez', 'address' => 'Calle 10 #5-20', 'email' => 'carlos@mail.com']);
        OwnerPhone::create(['owner_id' => $o1->id, 'phone_number' => '3001112233', 'phone_type' => 'Móvil']);

        $o2 = Owner::create(['document_type' => 'CC', 'document_number' => '10102', 'first_name' => 'María', 'last_name' => 'Gómez', 'address' => 'Carrera 4 #12-40', 'email' => 'maria@mail.com']);
        OwnerPhone::create(['owner_id' => $o2->id, 'phone_number' => '3002223344', 'phone_type' => 'Móvil']);

        $o3 = Owner::create(['document_type' => 'CC', 'document_number' => '10103', 'first_name' => 'Juan', 'last_name' => 'Rodríguez', 'address' => 'Av. Principal #8-15', 'email' => 'juan@mail.com']);
        OwnerPhone::create(['owner_id' => $o3->id, 'phone_number' => '3003334455', 'phone_type' => 'Móvil']);

        $o4 = Owner::create(['document_type' => 'CC', 'document_number' => '10104', 'first_name' => 'Ana', 'last_name' => 'Martínez', 'address' => 'Calle 15 #2-10', 'email' => 'ana@mail.com']);
        OwnerPhone::create(['owner_id' => $o4->id, 'phone_number' => '3004445566', 'phone_type' => 'Móvil']);

        $o5 = Owner::create(['document_type' => 'CC', 'document_number' => '10105', 'first_name' => 'Luis', 'last_name' => 'López', 'address' => 'Transversal 5 #9-30', 'email' => 'luis@mail.com']);
        OwnerPhone::create(['owner_id' => $o5->id, 'phone_number' => '3005556677', 'phone_type' => 'Móvil']);
    }
}
