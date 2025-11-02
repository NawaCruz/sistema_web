<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Compra;
use App\Models\User;
use App\Models\Proveedor;
use App\Models\MetodoPago;

class CompraSeeder extends Seeder
{
    public function run(): void
    {
        // Asegúrate de tener usuarios y proveedores creados
        $users = User::all();
        $proveedores = Proveedor::all();
        $metodosPago = MetodoPago::all();

        if ($users->isEmpty() || $proveedores->isEmpty()) {
            $this->command->warn('No hay usuarios o proveedores para generar compras.');
            return;
        }

        // Crear 10 compras aleatorias
        for ($i = 0; $i < 10; $i++) {
            Compra::create([
                'user_id' => $users->random()->id,
                'proveedor_id' => $proveedores->random()->id,
                'total' => 0,
                'metodo_pago_id' => fake()->randomElement($metodosPago)->id,
            ]);
        }
    }
}