<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MetodoPago;

class MetodoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $pagos = ['Efectivo', 'Tarjeta', 'Yape', 'Plin', 'Transferencia'];

        foreach ($pagos as $nombre) {
            MetodoPago::create(['nombre' => $nombre]);
        }
    }
}
