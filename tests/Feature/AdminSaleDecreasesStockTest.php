<?php

namespace Tests\Feature;

use App\Models\Caja;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSaleDecreasesStockTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Registrar una venta con stock suficiente
     * y verificar el descuento automático del stock.
     */
    public function test_sale_with_sufficient_stock_decreases_product_stock(): void
    {
        // Usuario admin autenticado
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        // Caja abierta para el usuario (requerida por VentaController)
        Caja::create([
            'id_user' => $admin->id,
            'monto_apertura' => 0,
            'monto_total' => 0,
            'monto_cierre' => null,
            'estado' => 'Abierta',
        ]);

        // Datos relacionados necesarios
        $cliente = Cliente::create([
            'dni' => '12345678',
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'correo' => 'cliente@example.com',
            'telefono' => '999999999',
            'direccion' => 'Direccion de prueba',
        ]);

        $metodoPago = MetodoPago::create([
            'nombre' => 'Efectivo',
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Categoria venta',
            'descripcion' => 'Categoria para venta',
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto venta',
            'descripcion' => 'Producto para prueba de venta',
            'precio_compra' => 10.00,
            'precio_venta' => 20.00,
            'stock' => 10,
            'descuento' => 0,
            'categoria_id' => $categoria->id,
            'proveedor_id' => null,
        ]);

        $cantidadVendida = 3;
        $descuento = 0;

        $payload = [
            'cliente_id' => $cliente->id,
            'user_id' => $admin->id,
            'metodo_pago_id' => $metodoPago->id,
            'total' => 0, // se recalcula en el backend
            'items' => [
                [
                    'id_producto' => $producto->id,
                    'cantidad' => $cantidadVendida,
                    'subtotal' => 0, // el backend recalcula
                    'descuento' => $descuento,
                ],
            ],
        ];

        $response = $this->postJson(route('admin.ventas.store'), $payload);

        // La creación de venta responde con 201 (Created)
        $response->assertStatus(201);
        $response->assertJson(['ok' => true]);

        $productoRefrescado = $producto->fresh();

        $this->assertEquals(
            10 - $cantidadVendida,
            $productoRefrescado->stock,
            'El stock del producto no se descontó correctamente después de la venta.'
        );
    }
}
