<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'dni' => '71234567',
                'nombre' => 'Carlos',
                'apellido' => 'Ramírez',
                'correo' => 'carlos.ramirez@example.com',
                'telefono' => '987654321',
                'direccion' => 'Av. Los Pinos 123 - Lima',
            ],
            [
                'dni' => '76543210',
                'nombre' => 'María',
                'apellido' => 'Gonzales',
                'correo' => 'maria.gonzales@example.com',
                'telefono' => '912345678',
                'direccion' => 'Jr. Las Flores 456 - Huancayo',
            ],
            [
                'dni' => '70123456',
                'nombre' => 'José',
                'apellido' => 'Fernández',
                'correo' => 'jose.fernandez@example.com',
                'telefono' => '934567890',
                'direccion' => 'Calle San Martín 789 - Arequipa',
            ],
            [
                'dni' => '70234567',
                'nombre' => 'Lucía',
                'apellido' => 'Torres',
                'correo' => 'lucia.torres@example.com',
                'telefono' => '945612378',
                'direccion' => 'Av. Grau 321 - Cusco',
            ],
            [
                'dni' => '70345678',
                'nombre' => 'Pedro',
                'apellido' => 'Castillo',
                'correo' => 'pedro.castillo@example.com',
                'telefono' => '956789012',
                'direccion' => 'Jr. Ayacucho 654 - Trujillo',
            ],
            [
                'dni' => '70456789',
                'nombre' => 'Ana',
                'apellido' => 'Mendoza',
                'correo' => 'ana.mendoza@example.com',
                'telefono' => '967890123',
                'direccion' => 'Av. Universitaria 987 - Lima',
            ],
            [
                'dni' => '70567890',
                'nombre' => 'Miguel',
                'apellido' => 'Sánchez',
                'correo' => 'miguel.sanchez@example.com',
                'telefono' => '978901234',
                'direccion' => 'Calle Los Olivos 111 - Piura',
            ],
            [
                'dni' => '70678901',
                'nombre' => 'Rosa',
                'apellido' => 'Lopez',
                'correo' => 'rosa.lopez@example.com',
                'telefono' => '989012345',
                'direccion' => 'Av. Bolognesi 222 - Tacna',
            ],
            [
                'dni' => '70789012',
                'nombre' => 'Luis',
                'apellido' => 'Martínez',
                'correo' => 'luis.martinez@example.com',
                'telefono' => '990123456',
                'direccion' => 'Jr. Independencia 333 - Chiclayo',
            ],
            [
                'dni' => '70890123',
                'nombre' => 'Patricia',
                'apellido' => 'Cruz',
                'correo' => 'patricia.cruz@example.com',
                'telefono' => '901234567',
                'direccion' => 'Av. Primavera 444 - Huánuco',
            ],
            [
                'dni' => '70901234',
                'nombre' => 'Fernando',
                'apellido' => 'García',
                'correo' => 'fernando.garcia@example.com',
                'telefono' => '912345679',
                'direccion' => 'Calle Central 555 - Puno',
            ],
            [
                'dni' => '71012345',
                'nombre' => 'Juana',
                'apellido' => 'Ríos',
                'correo' => 'juana.rios@example.com',
                'telefono' => '923456789',
                'direccion' => 'Jr. Amazonas 666 - Cajamarca',
            ],
            [
                'dni' => '71123456',
                'nombre' => 'Andrés',
                'apellido' => 'Morales',
                'correo' => 'andres.morales@example.com',
                'telefono' => '934567891',
                'direccion' => 'Av. Perú 777 - Iquitos',
            ],
            [
                'dni' => '71234568',
                'nombre' => 'Claudia',
                'apellido' => 'Pérez',
                'correo' => 'claudia.perez@example.com',
                'telefono' => '945678902',
                'direccion' => 'Calle Sol 888 - Tumbes',
            ],
            [
                'dni' => '71345679',
                'nombre' => 'Raúl',
                'apellido' => 'Delgado',
                'correo' => 'raul.delgado@example.com',
                'telefono' => '956789013',
                'direccion' => 'Av. Industrial 999 - Lima',
            ],
            [
                'dni' => '71456780',
                'nombre' => 'Elena',
                'apellido' => 'Flores',
                'correo' => 'elena.flores@example.com',
                'telefono' => '967890124',
                'direccion' => 'Jr. Libertad 101 - Cusco',
            ],
            [
                'dni' => '71567891',
                'nombre' => 'Diego',
                'apellido' => 'Ortega',
                'correo' => 'diego.ortega@example.com',
                'telefono' => '978901235',
                'direccion' => 'Av. Los Incas 202 - Ayacucho',
            ],
            [
                'dni' => '71678902',
                'nombre' => 'Gabriela',
                'apellido' => 'Salazar',
                'correo' => 'gabriela.salazar@example.com',
                'telefono' => '989012346',
                'direccion' => 'Calle Real 303 - Moquegua',
            ],
            [
                'dni' => '71789013',
                'nombre' => 'Hugo',
                'apellido' => 'Vargas',
                'correo' => 'hugo.vargas@example.com',
                'telefono' => '990123457',
                'direccion' => 'Av. Grau 404 - Arequipa',
            ],
            [
                'dni' => '71890124',
                'nombre' => 'Verónica',
                'apellido' => 'Suárez',
                'correo' => 'veronica.suarez@example.com',
                'telefono' => '901234568',
                'direccion' => 'Jr. San José 505 - Lima',
            ],
        ];

        foreach ($clientes as $c) {
            Cliente::create($c);
        }
    }
}
