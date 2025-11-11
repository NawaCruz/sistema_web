<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'admin',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        // Usuario empleado
        User::firstOrCreate(
            ['email' => 'empleado@example.com'],
            [
                'name'     => 'empleado',
                'password' => Hash::make('empleado123'),
                'role'     => 'empleado',
            ]
        );
    }
}
