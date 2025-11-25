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
     * pueda iniciar sesión con credenciales válidas.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        // Crear usuario administrador con contraseña por defecto "password"
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Enviar credenciales válidas al endpoint de login
        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        // Verificar que se redirige correctamente después del login
        $response->assertRedirect('/home');

        // Verificar que el usuario autenticado es el admin creado
        $this->assertAuthenticatedAs($admin);
        $this->assertEquals('admin', auth()->user()->role);
    }

    /**
     * Verifica que no se pueda iniciar sesión
     * cuando las credenciales son incorrectas.
     */
    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);

        // Debe redirigir de vuelta al formulario de login
        $response->assertRedirect('/login');

        // Debe registrar errores de validación en la sesión
        $response->assertSessionHasErrors('email');

        // El usuario no debe quedar autenticado
        $this->assertGuest();
    }
}
