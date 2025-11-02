<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'nombre' => 'AutoRepuestos S.A.',
                'ruc' => '20123456789',
                'telefono' => '987654321',
                'correo' => 'ventas@autorepuestos.com',
                'direccion' => 'Av. La Marina 123, Lima',
                'contacto' => 'Juan Pérez',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'ProAuto S.R.L.',
                'ruc' => '20456789123',
                'telefono' => '912345678',
                'correo' => 'contacto@proauto.com',
                'direccion' => 'Calle Los Olivos 456, Arequipa',
                'contacto' => 'María Ramírez',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'RefaParts E.I.R.L.',
                'ruc' => '20512345678',
                'telefono' => '998877665',
                'correo' => 'info@refaparts.pe',
                'direccion' => 'Jr. Puno 789, Huancayo',
                'contacto' => 'Carlos Gómez',
                'estado' => 'Inactivo'
            ],
            [
                'nombre' => 'LubriCentro El Motor',
                'ruc' => '20678912345',
                'telefono' => '965432187',
                'correo' => 'lubricantes@elmotor.com',
                'direccion' => 'Av. Grau 321, Trujillo',
                'contacto' => 'Ana López',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'Partes y Motores SAC',
                'ruc' => '20112233445',
                'telefono' => '999666333',
                'correo' => 'ventas@partesmotores.com',
                'direccion' => 'Av. Nicolás de Piérola 456, Cusco',
                'contacto' => 'Pedro Vargas',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'Inversiones Vehiculares S.A.C.',
                'ruc' => '20987654321',
                'telefono' => '944123456',
                'correo' => 'contacto@invervehic.com',
                'direccion' => 'Av. Industrial 1001, Piura',
                'contacto' => 'Lucía Medina',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'Soluciones Automotrices del Norte',
                'ruc' => '20765432109',
                'telefono' => '977888999',
                'correo' => 'soluciones@norteautomotriz.com',
                'direccion' => 'Mz. F Lt. 12, Chiclayo',
                'contacto' => 'Fernando Ruiz',
                'estado' => 'Inactivo'
            ],
            [
                'nombre' => 'Repuestos SurAndina',
                'ruc' => '20334455667',
                'telefono' => '955441122',
                'correo' => 'sur@repuestosandina.com',
                'direccion' => 'Av. Bolognesi 78, Tacna',
                'contacto' => 'Gabriela Salas',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'MegaAuto Perú',
                'ruc' => '20001112223',
                'telefono' => '998877445',
                'correo' => 'ventas@megaauto.pe',
                'direccion' => 'Calle Comercio 222, Lima',
                'contacto' => 'Luis Mendoza',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'Repuestos y Servicios Andes',
                'ruc' => '20991234567',
                'telefono' => '987112233',
                'correo' => 'servicios@andesparts.com',
                'direccion' => 'Av. Huancavelica 234, Huancayo',
                'contacto' => 'Teresa Paredes',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'TurboCentro S.A.C.',
                'ruc' => '20611223344',
                'telefono' => '923456789',
                'correo' => 'turbo@centro.com',
                'direccion' => 'Av. Venezuela 400, Lima',
                'contacto' => 'Oscar Chávez',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'Repuestera Nacional EIRL',
                'ruc' => '20556677889',
                'telefono' => '931234567',
                'correo' => 'nacional@repuestera.com',
                'direccion' => 'Calle Unión 110, Cajamarca',
                'contacto' => 'Elena Vargas',
                'estado' => 'Inactivo'
            ],
            [
                'nombre' => 'Centro Diesel Huancayo',
                'ruc' => '20339887766',
                'telefono' => '945112233',
                'correo' => 'diesel@huancayo.pe',
                'direccion' => 'Av. Mariscal Castilla 300, Huancayo',
                'contacto' => 'Julio Medina',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'Distribuidora ReMotor',
                'ruc' => '20228899556',
                'telefono' => '936998877',
                'correo' => 'ventas@remotor.pe',
                'direccion' => 'Calle Comercio 99, Ica',
                'contacto' => 'Rosario Huamán',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'FullTorque S.R.L.',
                'ruc' => '20447766554',
                'telefono' => '960445566',
                'correo' => 'fulltorque@peru.com',
                'direccion' => 'Av. Primavera 1200, Lima',
                'contacto' => 'Renzo Castillo',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'Andes Motor Import',
                'ruc' => '20995544332',
                'telefono' => '955667788',
                'correo' => 'contacto@andesmotor.com',
                'direccion' => 'Jr. Tarapacá 345, Cusco',
                'contacto' => 'Milagros Rivas',
                'estado' => 'Inactivo'
            ],
            [
                'nombre' => 'GlobalParts EIRL',
                'ruc' => '20771112233',
                'telefono' => '941122334',
                'correo' => 'info@globalparts.com',
                'direccion' => 'Av. América Sur 800, Trujillo',
                'contacto' => 'Andrés Carranza',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'MotorTech Solutions',
                'ruc' => '20033445566',
                'telefono' => '987009988',
                'correo' => 'support@motortech.pe',
                'direccion' => 'Calle Lima 154, Arequipa',
                'contacto' => 'Camila Ayala',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'ServiMotor Express',
                'ruc' => '20991112233',
                'telefono' => '976543210',
                'correo' => 'servicios@servimotor.com',
                'direccion' => 'Av. Brasil 456, Callao',
                'contacto' => 'Héctor Ramos',
                'estado' => 'Activo'
            ],
            [
                'nombre' => 'Repuestos Vega',
                'ruc' => '20334455678',
                'telefono' => '934567891',
                'correo' => 'ventas@repuestosvega.pe',
                'direccion' => 'Jr. Callao 78, Puno',
                'contacto' => 'Verónica Vega',
                'estado' => 'Inactivo'
            ]
        ];

        foreach ($proveedores as $p) {
            Proveedor::create($p);
        }
    }
}
