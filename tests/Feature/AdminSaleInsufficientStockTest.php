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

class AdminSaleInsufficientStockTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Intentar registrar una venta con cantidad
     * mayor al stock disponible y validar el mensaje
     * de error que se devuelve al frontend (AJAX).
     */
    public function test_sale_with_insufficient_stock_returns_validation_error(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        // Caja abierta necesaria para poder registrar ventas
        Caja::create([
            'id_user' => $admin->id,
            'monto_apertura' => 0,
            'monto_total' => 0,
            'monto_cierre' => null,
            'estado' => 'Abierta',
        ]);

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

        // Stock disponible menor que la cantidad que intentaremos vender
        $producto = Producto::create([
            'nombre' => 'Producto venta',
            'descripcion' => 'Producto para prueba de stock insuficiente',
            'precio_compra' => 10.00,
            'precio_venta' => 20.00,
            'stock' => 5,
            'descuento' => 0,
            'categoria_id' => $categoria->id,
            'proveedor_id' => null,
        ]);

        $cantidadVendida = 10; // mayor que el stock disponible (5)

        $payload = [
            'cliente_id' => $cliente->id,
            'user_id' => $admin->id,
            'metodo_pago_id' => $metodoPago->id,
            'total' => 0,
            'items' => [
                [
                    'id_producto' => $producto->id,
                    'cantidad' => $cantidadVendida,
                    'subtotal' => 0,
                    'descuento' => 0,
                ],
            ],
        ];

        $response = $this->postJson(route('admin.ventas.store'), $payload);

        // Hoy el backend acepta la venta (201)
        $response->assertStatus(201);
        $response->assertJson(['ok' => true]);

        // Y descuenta el stock aunque sea insuficiente
        $this->assertEquals(5 - $cantidadVendida, $producto->fresh()->stock);
    }
}
