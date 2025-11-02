<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MlClient;

class FidelizacionController extends Controller
{
    public function predecir(Request $req, MlClient $ml) {
        $data = $req->validate([
            'frecuencia_compra'   => 'required|numeric|min:0',
            'monto_promedio'      => 'required|numeric|min:0',
            'dias_ultima_compra'  => 'required|numeric|min:0',
        ]);

        $pred = $ml->predict([$data])[0]; // una sola fila
        return response()->json($pred);
    }
}
