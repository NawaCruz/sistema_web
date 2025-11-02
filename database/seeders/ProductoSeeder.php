<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener categorías por nombre (clave = nombre, valor = id)
        $categorias = Categoria::pluck('id', 'nombre');

        // Obtener primer proveedor (o muestra advertencia)
        $proveedorId = Proveedor::first()?->id;

        if (!$proveedorId) {
            $this->command->warn('⚠️ No se encontraron proveedores. Crea uno antes de correr este seeder.');
            return;
        }

        $productos = [
            ['nombre' => 'Lavado completo', 'categoria' => 'servicio', 'stock' => 10, 'precio_venta' => 25.00, 'precio_compra' => 18.00],
            ['nombre' => 'Aspirado interno', 'categoria' => 'servicio', 'stock' => 20, 'precio_venta' => 15.00, 'precio_compra' => 10.00],
            ['nombre' => 'Tapizado de asientos', 'categoria' => 'tapicería', 'stock' => 5, 'precio_venta' => 120.00, 'precio_compra' => 95.00],
            ['nombre' => 'Reparación de asientos', 'categoria' => 'tapicería', 'stock' => 8, 'precio_venta' => 90.00, 'precio_compra' => 70.00],
            ['nombre' => 'Polarizado oscuro', 'categoria' => 'polarizado', 'stock' => 12, 'precio_venta' => 60.00, 'precio_compra' => 40.00],
            ['nombre' => 'Polarizado intermedio', 'categoria' => 'polarizado', 'stock' => 14, 'precio_venta' => 55.00, 'precio_compra' => 35.00],
            ['nombre' => 'Laminado UV', 'categoria' => 'laminado', 'stock' => 6, 'precio_venta' => 80.00, 'precio_compra' => 55.00],
            ['nombre' => 'Laminado antirrayas', 'categoria' => 'laminado', 'stock' => 4, 'precio_venta' => 95.00, 'precio_compra' => 70.00],
            ['nombre' => 'Accesorio GPS', 'categoria' => 'otros', 'stock' => 10, 'precio_venta' => 150.00, 'precio_compra' => 100.00],
            ['nombre' => 'Aromatizante', 'categoria' => 'otros', 'stock' => 25, 'precio_venta' => 8.00, 'precio_compra' => 4.00],
            ['nombre' => 'Cambio de aceite', 'categoria' => 'servicio', 'stock' => 7, 'precio_venta' => 70.00, 'precio_compra' => 50.00],
            ['nombre' => 'Corte y costura tapiz', 'categoria' => 'tapicería', 'stock' => 3, 'precio_venta' => 110.00, 'precio_compra' => 85.00],
            ['nombre' => 'Kit de polarizado', 'categoria' => 'polarizado', 'stock' => 9, 'precio_venta' => 45.00, 'precio_compra' => 30.00],
            ['nombre' => 'Lámina de seguridad', 'categoria' => 'laminado', 'stock' => 6, 'precio_venta' => 85.00, 'precio_compra' => 60.00],
            ['nombre' => 'Porta celular', 'categoria' => 'otros', 'stock' => 30, 'precio_venta' => 12.00, 'precio_compra' => 6.00],
            ['nombre' => 'Pulido exterior', 'categoria' => 'servicio', 'stock' => 5, 'precio_venta' => 65.00, 'precio_compra' => 45.00],
            ['nombre' => 'Tapiz impermeable', 'categoria' => 'tapicería', 'stock' => 4, 'precio_venta' => 130.00, 'precio_compra' => 100.00],
            ['nombre' => 'Laminado reflectante', 'categoria' => 'laminado', 'stock' => 5, 'precio_venta' => 90.00, 'precio_compra' => 65.00],
            ['nombre' => 'Cargador USB', 'categoria' => 'otros', 'stock' => 20, 'precio_venta' => 18.00, 'precio_compra' => 10.00],
            ['nombre' => 'Revisión técnica express', 'categoria' => 'servicio', 'stock' => 10, 'precio_venta' => 100.00, 'precio_compra' => 75.00],
        ];

        foreach ($productos as $item) {
            // Verificamos que la categoría exista
            $categoriaId = $categorias[$item['categoria']] ?? null;

            if ($categoriaId) {
                Producto::create([
                    'nombre'         => $item['nombre'],
                    'descripcion'    => 'Producto: ' . $item['nombre'],
                    'precio_compra'  => $item['precio_compra'],
                    'precio_venta'   => $item['precio_venta'],
                    'stock'          => $item['stock'],
                    'descuento'      => 0, // Puedes personalizar esto si deseas
                    'categoria_id'   => $categoriaId,
                    'proveedor_id'   => $proveedorId,
                ]);
            } else {
                $this->command->warn("⚠️ La categoría '{$item['categoria']}' no fue encontrada.");
            }
        }
    }
}
