<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropiedadSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('propiedades')->truncate();

        try {
            DB::table('propiedades')->insert([
                [
                    'titulo' => 'Apartamento Moderno en Chapinero',
                    'descripcion' => 'Hermoso apartamento de 3 habitaciones con acabados de lujo, cocina integral, balcón con vista panorámica y parqueadero.',
                    'ubicacion' => 'Chapinero, Bogotá',
                    'ciudad' => 'Bogotá',
                    'categoria_ciudad' => 'Urbano',
                    'tamano_m2' => 85.00,
                    'precio' => 450000000.00,
                    'estado' => 'disponible',
                    'tipo' => 'apartamento',
                    'nitInmobiliaria' => '900123456-1',
                    'imagen' => 'storage/propiedades/thumb_casa_1.jpg',
                    'modelo_3d_path' => 'storage/propiedades/modelos/casa_demo_1/',
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ],
                [
                    'titulo' => 'Casa Campestre en La Calera',
                    'descripcion' => 'Espectacular casa campestre con 4 habitaciones, chimenea, jardín amplio, zona BBQ y vista a las montañas.',
                    'ubicacion' => 'La Calera, Cundinamarca',
                    'ciudad' => 'La Calera',
                    'categoria_ciudad' => 'Campestre',
                    'tamano_m2' => 220.00,
                    'precio' => 850000000.00,
                    'estado' => 'disponible',
                    'tipo' => 'casa',
                    'nitInmobiliaria' => '900123456-1',
                    'imagen' => 'storage/propiedades/thumb_casa_2.jpg',
                    'modelo_3d_path' => 'storage/propiedades/modelos/casa_demo_2/',
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ],
                [
                    'titulo' => 'Penthouse El Chicó Premium',
                    'descripcion' => 'Penthouse de lujo con ventanales de piso a techo, terraza privada de 40m2 y sistema domótico integrado.',
                    'ubicacion' => 'El Chicó, Bogotá',
                    'ciudad' => 'Bogotá',
                    'categoria_ciudad' => 'Exclusivo',
                    'tamano_m2' => 180.00,
                    'precio' => 1200000000.00,
                    'estado' => 'disponible',
                    'tipo' => 'apartamento',
                    'nitInmobiliaria' => '900123456-1',
                    'imagen' => 'storage/propiedades/thumb_casa_3.jpg',
                    'modelo_3d_path' => 'storage/propiedades/modelos/casa_demo_3/',
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ],
                [
                    'titulo' => 'Loft Industrial en Usaquén',
                    'descripcion' => 'Diseño vanguardista con techos de doble altura, paredes de ladrillo a la vista y excelente iluminación natural.',
                    'ubicacion' => 'Usaquén, Bogotá',
                    'ciudad' => 'Bogotá',
                    'categoria_ciudad' => 'Urbano',
                    'tamano_m2' => 75.00,
                    'precio' => 310000000.00,
                    'estado' => 'disponible',
                    'tipo' => 'apartamento',
                    'nitInmobiliaria' => '900987654-2',
                    'imagen' => 'storage/propiedades/thumb_casa_default.jpg',
                    'modelo_3d_path' => null,
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ],
                [
                    'titulo' => 'Dúplex en Santa Bárbara',
                    'descripcion' => 'Dúplex remodelado con acabados premium, cocina tipo americana y dos balcones.',
                    'ubicacion' => 'Santa Bárbara, Bogotá',
                    'ciudad' => 'Bogotá',
                    'categoria_ciudad' => 'Urbano',
                    'tamano_m2' => 110.00,
                    'precio' => 520000000.00,
                    'estado' => 'disponible',
                    'tipo' => 'apartamento',
                    'nitInmobiliaria' => '900987654-2',
                    'imagen' => 'storage/propiedades/thumb_casa_default.jpg',
                    'modelo_3d_path' => null,
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ],
                [
                    'titulo' => 'Apartamento en Cedritos',
                    'descripcion' => 'Apartamento de 3 habitaciones, 2 baños, sala-comedor amplia, cocina integral y cuarto útil.',
                    'ubicacion' => 'Cedritos, Bogotá',
                    'ciudad' => 'Bogotá',
                    'categoria_ciudad' => 'Familiar',
                    'tamano_m2' => 95.00,
                    'precio' => 380000000.00,
                    'estado' => 'disponible',
                    'tipo' => 'apartamento',
                    'nitInmobiliaria' => '900123456-1',
                    'imagen' => 'storage/propiedades/thumb_casa_default.jpg',
                    'modelo_3d_path' => null,
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ],
                [
                    'titulo' => 'Oficina en Zona Rosa (Vendida)',
                    'descripcion' => 'Oficina moderna en el corazón de la Zona Rosa, ideal para empresas de tecnología.',
                    'ubicacion' => 'Zona Rosa, Bogotá',
                    'ciudad' => 'Bogotá',
                    'categoria_ciudad' => 'Corporativo',
                    'tamano_m2' => 65.00,
                    'precio' => 320000000.00,
                    'estado' => 'vendida',
                    'tipo' => 'oficina',
                    'nitInmobiliaria' => '900987654-2',
                    'imagen' => 'storage/propiedades/thumb_casa_default.jpg',
                    'modelo_3d_path' => null,
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ],
                [
                    'titulo' => 'Local Comercial en Suba (Vendido)',
                    'descripcion' => 'Local comercial en zona de alto tráfico, ideal para restaurante o tienda.',
                    'ubicacion' => 'Suba, Bogotá',
                    'ciudad' => 'Bogotá',
                    'categoria_ciudad' => 'Comercial',
                    'tamano_m2' => 120.00,
                    'precio' => 280000000.00,
                    'estado' => 'vendida',
                    'tipo' => 'local',
                    'nitInmobiliaria' => '900987654-2',
                    'imagen' => 'storage/propiedades/thumb_casa_default.jpg',
                    'modelo_3d_path' => null,
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ],
                [
                    'titulo' => 'Casa Unifamiliar Modelia (Vendida)',
                    'descripcion' => 'Casa de dos pisos con garaje cubierto y amplio patio trasero.',
                    'ubicacion' => 'Modelia, Bogotá',
                    'ciudad' => 'Bogotá',
                    'categoria_ciudad' => 'Familiar',
                    'tamano_m2' => 160.00,
                    'precio' => 600000000.00,
                    'estado' => 'vendida',
                    'tipo' => 'casa',
                    'nitInmobiliaria' => '900123456-1',
                    'imagen' => 'storage/propiedades/thumb_casa_default.jpg',
                    'modelo_3d_path' => null,
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ]
            ]);
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            dd($e->getMessage());
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
