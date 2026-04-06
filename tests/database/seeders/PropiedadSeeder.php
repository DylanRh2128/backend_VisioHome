<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropiedadSeeder extends Seeder
{
    public function run()
    {
        DB::table('propiedades')->insertOrIgnore([
            [
                'titulo' => 'Apartamento Moderno en Chapinero',
                'descripcion' => 'Hermoso apartamento de 3 habitaciones con acabados de lujo, cocina integral, balcón con vista panorámica y parqueadero.',
                'ubicacion' => 'Chapinero, Bogotá',
                'tamano_m2' => 85.00,
                'precio' => 450000000.00,
                'estado' => 'disponible',
                'tipo' => 'apartamento',
                'nitInmobiliaria' => '900123456-1',
                'creado_en' => now(),
                'actualizado_en' => now()
            ],
            [
                'titulo' => 'Casa Campestre en La Calera',
                'descripcion' => 'Espectacular casa campestre con 4 habitaciones, chimenea, jardín amplio, zona BBQ y vista a las montañas.',
                'ubicacion' => 'La Calera, Cundinamarca',
                'tamano_m2' => 220.00,
                'precio' => 850000000.00,
                'estado' => 'vendida',
                'tipo' => 'casa',
                'nitInmobiliaria' => '900123456-1',
                'creado_en' => now(),
                'actualizado_en' => now()
            ],
            [
                'titulo' => 'Oficina en Zona Rosa',
                'descripcion' => 'Oficina moderna en el corazón de la Zona Rosa, ideal para empresas de tecnología. Incluye 2 baños y recepción.',
                'ubicacion' => 'Zona Rosa, Bogotá',
                'tamano_m2' => 65.00,
                'precio' => 320000000.00,
                'estado' => 'arrendada',
                'tipo' => 'oficina',
                'nitInmobiliaria' => '900987654-2',
                'creado_en' => now(),
                'actualizado_en' => now()
            ],
            [
                'titulo' => 'Apartamento Familiar en Cedritos',
                'descripcion' => 'Apartamento de 3 habitaciones, 2 baños, sala-comedor amplia, cocina integral y cuarto útil.',
                'ubicacion' => 'Cedritos, Bogotá',
                'tamano_m2' => 95.00,
                'precio' => 380000000.00,
                'estado' => 'disponible',
                'tipo' => 'apartamento',
                'nitInmobiliaria' => '900123456-1',
                'creado_en' => now(),
                'actualizado_en' => now()
            ],
            [
                'titulo' => 'Local Comercial en Suba',
                'descripcion' => 'Local comercial en zona de alto tráfico, ideal para restaurante o tienda. Incluye baño y bodega.',
                'ubicacion' => 'Suba, Bogotá',
                'tamano_m2' => 120.00,
                'precio' => 280000000.00,
                'estado' => 'reservada',
                'tipo' => 'local',
                'nitInmobiliaria' => '900987654-2',
                'creado_en' => now(),
                'actualizado_en' => now()
            ]
        ]);
    }
}
