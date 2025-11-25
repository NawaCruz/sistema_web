<?php

namespace Tests\Feature;

use App\Models\Caja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CajaCloseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cerrar caja calculando correctamente el monto de cierre
     * y verificando que quede con estado "Cerrada".
     */
    public function test_admin_can_close_caja_and_persist_closing_amount(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        // Caja abierta con un monto total simulado (por ejemplo, ventas realizadas)
        $caja = Caja::create([
            'id_user' => $admin->id,
            'monto_apertura' => 100.00,
            'monto_total' => 250.00,
            'monto_cierre' => null,
            'estado' => 'Abierta',
        ]);

        $montoCierre = 260.00;

        // Cerrar la caja vía la ruta de actualización
        $response = $this->put(route('admin.cajas.update', $caja->id_caja), [
            'monto_cierre' => $montoCierre,
        ]);

        // Debe redirigir al índice de cajas del admin
        $response->assertRedirect(route('admin.cajas.index'));

        $cajaRefrescada = $caja->fresh();

        // Verificar que la caja quedó cerrada con el monto de cierre correcto
        $this->assertEquals('Cerrada', $cajaRefrescada->estado);
        $this->assertEquals($montoCierre, (float) $cajaRefrescada->monto_cierre);

        // El saldo final puede interpretarse como diferencia contra el monto_total
        $saldoFinal = round($montoCierre - (float) $cajaRefrescada->monto_total, 2);
        $this->assertEquals(10.00, $saldoFinal);
    }
}

