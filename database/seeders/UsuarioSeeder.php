<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        // Admin principal con credenciales específicas
        DB::table('usuarios')->updateOrInsert([
            'docUsuario' => '1033183120',
            'nombre' => 'Dylan Rios',
            'correo' => 'dylanrios211@gmail.com',
            'telefono' => '3135057694',
            'direccion' => 'Carrera 31 #75c-44',
            'password' => Hash::make('2128Henao*'),
            'idRol' => 1, // Admin
            'creado_en' => now(),
            'actualizado_en' => null,
            'intentosFallidos' => 0,
            'bloqueadoHasta' => null
        ]);

        // Usuarios adicionales de prueba
        DB::table('usuarios')->insertOrIgnore([
            [
                'docUsuario' => '1001111111',
                'nombre' => 'Juan Pérez',
                'correo' => 'juan.perez@example.com',
                'telefono' => '3001234567',
                'direccion' => 'Calle 50 #10-20',
                'password' => Hash::make('password123'),
                'idRol' => 2, // Cliente
                'creado_en' => now(),
                'actualizado_en' => null,
                'intentosFallidos' => 0,
                'bloqueadoHasta' => null
            ],
            [
                'docUsuario' => '1002222222',
                'nombre' => 'María García',
                'correo' => 'maria.garcia@example.com',
                'telefono' => '3009876543',
                'direccion' => 'Avenida 68 #45-30',
                'password' => Hash::make('password123'),
                'idRol' => 3, // Agente
                'creado_en' => now(),
                'actualizado_en' => null,
                'intentosFallidos' => 0,
                'bloqueadoHasta' => null
            ],
            [
                'docUsuario' => '1003333333',
                'nombre' => 'Carlos López',
                'correo' => 'carlos.lopez@example.com',
                'telefono' => '3015551234',
                'direccion' => 'Carrera 15 #80-10',
                'password' => Hash::make('password123'),
                'idRol' => 2, // Cliente
                'creado_en' => now(),
                'actualizado_en' => null,
                'intentosFallidos' => 0,
                'bloqueadoHasta' => null
            ]
        ]);
    }
}
