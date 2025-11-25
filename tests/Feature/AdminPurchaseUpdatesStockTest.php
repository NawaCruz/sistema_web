<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Compra;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPurchaseUpdatesStockTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Registrar una compra y comprobar
     * la actualización del stock del producto.
     */
    public function test_register_purchase_updates_product_stock(): void
    {
        // Usuario admin autenticado
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        // Datos relacionados requeridos
        $categoria = Categoria::create([
            'nombre' => 'Categoria de prueba',
            'descripcion' => 'Descripcion de prueba',
        ]);

        $proveedor = Proveedor::create([
            'nombre' => 'Proveedor de prueba',
            'ruc' => '12345678901',
            'telefono' => '999999999',
            'correo' => 'proveedor@example.com',
            'direccion' => 'Direccion de prueba',
            'contacto' => 'Contacto de prueba',
            'estado' => 'Activo',
        ]);

        $metodoPago = MetodoPago::create([
            'nombre' => 'Efectivo',
        ]);

        // Producto inicial con stock conocido
        $producto = Producto::create([
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Descripcion del producto de prueba',
            'precio_compra' => 10.50,
            'precio_venta' => 15.75,
            'stock' => 5,
            'descuento' => 0,
            'categoria_id' => $categoria->id,
            'proveedor_id' => $proveedor->id,
        ]);

        // Registrar la compra
        $responseCompra = $this->post(route('admin.compras.store'), [
            'proveedor_id' => $proveedor->id,
            'metodo_pago_id' => $metodoPago->id,
            'total' => 0, // se recalculará con los detalles
            'user_id' => $admin->id,
        ]);

        $responseCompra->assertRedirect(route('admin.compras.index'));

        $compra = Compra::first();

        // Registrar el detalle de la compra (cantidad comprada)
        $cantidadComprada = 3;

        $responseDetalle = $this->post(route('admin.detalleCompras.store'), [
            'id_compra' => $compra->id,
            'id_producto' => $producto->id,
            'cantidad' => $cantidadComprada,
            'precio_unitario' => 10.50,
            'subtotal' => 10.50 * $cantidadComprada,
        ]);

        $responseDetalle->assertRedirect(route('admin.detalleCompras.index'));

        // Comprobar que el stock del producto se actualizó
        $productoRefrescado = $producto->fresh();

        $this->assertEquals(
            5 + $cantidadComprada,
            $productoRefrescado->stock,
            'El stock del producto no se actualizó correctamente después de registrar la compra.'
        );
    }
}

