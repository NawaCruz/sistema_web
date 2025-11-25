<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que un usuario con rol admin
     * pueda iniciar sesion con credenciales validas.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        // Crear usuario administrador con contrasena por defecto "password"
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Enviar credenciales validas al endpoint de login
        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        // Verificar que se redirige correctamente despues del login
        $response->assertRedirect('/home');

        // Verificar que el usuario autenticado es el admin creado
        $this->assertAuthenticatedAs($admin);
        $this->assertEquals('admin', auth()->user()->role);
    }
}
