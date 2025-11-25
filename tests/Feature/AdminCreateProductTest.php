<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCreateProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Registrar un nuevo producto con todos
     * los campos obligatorios completos.
     */
    public function test_admin_can_create_product_with_all_required_fields(): void
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

        // Payload con todos los campos obligatorios (y algunos opcionales)
        $payload = [
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Descripcion del producto de prueba',
            'precio_compra' => 10.50,
            'precio_venta' => 15.75,
            'stock' => 100,
            'descuento' => 5.0,
            'categoria_id' => $categoria->id,
            'proveedor_id' => $proveedor->id,
        ];

        // Ejecutar el request al endpoint de creación de productos como admin
        $response = $this->post(route('admin.productos.store'), $payload);

        // Debe redirigir al índice de productos del admin
        $response->assertRedirect(route('admin.productos.index'));

        // Verificar que el producto se creó en la base de datos
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto de prueba',
            'precio_venta' => 15.75,
            'stock' => 100,
            'categoria_id' => $categoria->id,
            'proveedor_id' => $proveedor->id,
        ]);
    }
}

