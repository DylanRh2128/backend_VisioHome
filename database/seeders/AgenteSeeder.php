<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgenteSeeder extends Seeder
{
    public function run()
    {
        DB::table('agentes')->insertOrIgnore([
            [
                'docAgente' => '1001234567',
                'nombre' => 'Ana Martínez',
                'direccion' => 'Calle 85 #12-34, Bogotá',
                'correo' => 'ana.martinez@visiohome.com',
                'telefono' => '+57 300 123 4567',
                'nitInmobiliaria' => '900123456-1',
                'activo' => 1
            ],
            [
                'docAgente' => '1002345678',
                'nombre' => 'Roberto Silva',
                'direccion' => 'Carrera 15 #90-12, Bogotá',
                'correo' => 'roberto.silva@visiohome.com',
                'telefono' => '+57 301 234 5678',
                'nitInmobiliaria' => '900123456-1',
                'activo' => 1
            ],
            [
                'docAgente' => '1003456789',
                'nombre' => 'Laura Gómez',
                'direccion' => 'Avenida 19 #120-45, Bogotá',
                'correo' => 'laura.gomez@visiohome.com',
                'telefono' => '+57 302 345 6789',
                'nitInmobiliaria' => '900987654-2',
                'activo' => 0
            ]
        ]);
    }
}
