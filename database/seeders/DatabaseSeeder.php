<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CategoriaSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ProductoSeeder;
use Database\Seeders\MetodoPagoSeeder;
use Database\Seeders\ProveedorSeeder;
use Database\Seeders\CompraSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            UserSeeder::class,
            CategoriaSeeder::class,
            MetodoPagoSeeder::class,
            ProveedorSeeder::class,
            ProductoSeeder::class,
            CompraSeeder::class,
            DetalleComprasSeeder::class,
            ClienteSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
