<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\DetalleVenta;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Acceder al dashboard de administrador y
     * visualizar indicadores y gráficos con datos consistentes.
     */
    public function test_admin_dashboard_shows_consistent_indicators_and_charts(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        // Datos base
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

        $proveedor = Proveedor::create([
            'nombre' => 'Proveedor de prueba',
            'ruc' => '12345678901',
            'telefono' => '999999999',
            'correo' => 'proveedor@example.com',
            'direccion' => 'Direccion de prueba',
            'contacto' => 'Contacto de prueba',
            'estado' => 'Activo',
        ]);

        $productoCritico = Producto::create([
            'nombre' => 'Producto critico',
            'descripcion' => 'Producto con stock bajo',
            'precio_compra' => 10.00,
            'precio_venta' => 20.00,
            'stock' => 3, // <= umbralCritico (5)
            'descuento' => 0,
            'categoria_id' => null,
            'proveedor_id' => null,
        ]);

        $productoNormal = Producto::create([
            'nombre' => 'Producto normal',
            'descripcion' => 'Producto con stock suficiente',
            'precio_compra' => 15.00,
            'precio_venta' => 30.00,
            'stock' => 10,
            'descuento' => 0,
            'categoria_id' => null,
            'proveedor_id' => null,
        ]);

        // Venta de hoy por 100
        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'user_id' => $admin->id,
            'metodo_pago_id' => $metodoPago->id,
            'total' => 100.00,
        ]);

        DetalleVenta::create([
            'id_venta' => $venta->id,
            'id_producto' => $productoCritico->id,
            'cantidad' => 2,
            'precio_unitario' => 50.00,
            'descuento' => 0,
            'subtotal' => 100.00,
        ]);

        // Compra de este mes por 40
        Compra::create([
            'user_id' => $admin->id,
            'proveedor_id' => $proveedor->id,
            'metodo_pago_id' => $metodoPago->id,
            'total' => 40.00,
        ]);

        $response = $this->get(route('admin.dashboard.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard.index');

        // Indicadores numéricos básicos
        $response->assertViewHas('ventasHoy', 100.00);
        $response->assertViewHas('ventasMes', 100.00);
        $response->assertViewHas('ingresos', 100.00);
        $response->assertViewHas('egresos', 40.00);
        $response->assertViewHas('utilidad', 60.00);

        // Totales de productos y clientes
        $response->assertViewHas('totalProductos', 2);
        $response->assertViewHas('totalClientes', 1);

        // Stock crítico (un producto con stock <= 5)
        $response->assertViewHas('stockCritico', 1);

        // Gráficos: solo validamos que tengan estructura y datos no vacíos
        $response->assertViewHas('ventasPorMes', function ($v) {
            return is_array($v)
                && isset($v['labels'], $v['data'])
                && count($v['data']) >= 1;
        });

        $response->assertViewHas('topProductos', function ($v) {
            return is_array($v)
                && isset($v['labels'], $v['data'])
                && count($v['data']) >= 1;
        });

        $response->assertViewHas('stock', function ($v) {
            return is_array($v)
                && isset($v['labels'], $v['data'])
                && count($v['data']) === 2;
        });

        $response->assertViewHas('ingEgr', function ($v) {
            return is_array($v)
                && isset($v['labels'], $v['ingresos'], $v['egresos']);
        });

        $response->assertViewHas('tiposPago', function ($v) {
            return is_array($v)
                && isset($v['labels'], $v['data']);
        });
    }
}

