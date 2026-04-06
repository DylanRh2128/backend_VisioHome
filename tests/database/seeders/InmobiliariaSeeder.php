<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InmobiliariaSeeder extends Seeder
{
    public function run()
    {
        DB::table('inmobiliarias')->insertOrIgnore([
            [
                'nitInmobiliaria' => '900123456-1',
                'nombre' => 'VisioHome Inmobiliaria',
                'correo' => 'contacto@visiohome.com',
                'telefono' => '+57 601 234 5678',
                'direccion' => 'Calle 100 #15-20, Bogotá',
                'objetivo' => 'Líder en soluciones inmobiliarias con tecnología de realidad aumentada'
            ],
            [
                'nitInmobiliaria' => '900987654-2',
                'nombre' => 'Propiedades Premium',
                'correo' => 'info@premiumprop.com',
                'telefono' => '+57 601 987 6543',
                'direccion' => 'Carrera 7 #80-45, Bogotá',
                'objetivo' => 'Propiedades de lujo en las mejores zonas de la ciudad'
            ]
        ]);
    }
}
