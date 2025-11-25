<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MlClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FidelizacionPredictTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Enviar datos validos de un cliente al endpoint
     * de fidelizacion y verificar clasificacion y probabilidad.
     */
    public function test_fidelizacion_endpoint_returns_classification_and_probability(): void
    {
        // Usuario admin autenticado
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        // Stub de MlClient para no llamar al servicio real
        $fakeMl = new class extends MlClient {
            public array $received = [];

            public function predict(array $items): array
            {
                $this->received = $items;

                return [[
                    'y_pred' => 'fiel',
                    'prob_fiel' => 0.87,
                ]];
            }
        };

        $this->app->instance(MlClient::class, $fakeMl);

        $payload = [
            'frecuencia_compra' => 5,
            'monto_promedio' => 150.0,
            'dias_ultima_compra' => 10,
        ];

        $response = $this->postJson(
            route('admin.ml.fidelizacion.predict'),
            $payload
        );

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'y_pred' => 'fiel',
        ]);
        $response->assertJsonStructure([
            'y_pred',
            'prob_fiel',
        ]);

        // Verificar que el cliente ML recibio exactamente una fila con los datos enviados
        // (sin exigir coincidencia estricta de tipos numéricos)
        $this->assertEquals(
            [$payload],
            $fakeMl->received
        );
    }
}
