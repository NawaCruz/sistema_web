<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DetalleCompra;
use App\Models\Compra;
use App\Models\Producto;

class DetalleComprasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $compras = Compra::all();
        $productos = Producto::all();

        if ($compras->count() === 0 || $productos->count() === 0) {
            $this->command->warn('No hay compras o productos disponibles para crear detalles.');
            return;
        }

        // Crear 20 detalles de compra aleatorios
        for ($i = 0; $i < 20; $i++) {
            $compra = $compras->random();
            $producto = $productos->random();
            $cantidad = $faker->numberBetween(1, 10);
            $precio_unitario = $producto->precio_compra ?? $faker->randomFloat(2, 1, 100);

            DetalleCompra::create([
                'id_compra' => $compra->id,
                'id_producto' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio_unitario,
                'subtotal' => $cantidad * $precio_unitario,
            ]);
        }
    }
}
