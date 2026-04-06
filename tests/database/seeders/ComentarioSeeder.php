<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComentarioSeeder extends Seeder
{
    public function run()
    {
        $usuario = DB::table('usuarios')->first();
        $propiedad = DB::table('propiedades')->first();

        if ($usuario && $propiedad) {
            DB::table('comentarios_propiedad')->insert([
                [
                    'idPropiedad' => $propiedad->idPropiedad,
                    'docUsuario' => $usuario->docUsuario,
                    'comentario' => 'Excelente propiedad, muy bien ubicada y en perfecto estado.',
                    'puntuacion' => 5,
                    'fecha' => now()->subDays(2)
                ],
                [
                    'idPropiedad' => $propiedad->idPropiedad,
                    'docUsuario' => $usuario->docUsuario,
                    'comentario' => 'Me gustaría recibir más información sobre los planes de financiación.',
                    'puntuacion' => 4,
                    'fecha' => now()->subMinutes(30)
                ]
            ]);
        }
    }
}
