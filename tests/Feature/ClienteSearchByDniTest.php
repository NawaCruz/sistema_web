<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteSearchByDniTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Registrar un nuevo cliente y luego
     * buscarlo por DNI usando la ruta de búsqueda.
     */
    public function test_can_register_client_and_find_by_dni(): void
    {
        // Usuario admin autenticado (puede usar rutas /admin/...)
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        // Registrar cliente en la base de datos
        $cliente = Cliente::create([
            'dni' => '12345678',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'correo' => 'juan.perez@example.com',
            'telefono' => '999999999',
            'direccion' => 'Direccion de prueba',
        ]);

        // Buscarlo por DNI usando la ruta de búsqueda de admin
        $response = $this->getJson(route('admin.clientes.buscarPorDni', [
            'dni' => $cliente->dni,
        ]));

        $response->assertStatus(200);
        $response
            ->assertJsonFragment([
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'dni' => $cliente->dni,
            ]);
    }
}

