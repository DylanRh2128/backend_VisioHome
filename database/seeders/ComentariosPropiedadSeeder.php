<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ComentariosPropiedadSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'comentarios_propiedad';
        $path = database_path("sql/visiohome/visiohome_{$table}.sql");

        if (!File::exists($path)) return;

        try {
            $sql = File::get($path);
            $sql = str_replace('`', '"', $sql);
            preg_match_all('/INSERT INTO "comentarios_propiedad" VALUES \((.+?)\);/is', $sql, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $values) {
                    $valArray = explode(',', $values);
                    $idPropiedad = (int)trim($valArray[1]);

                    // Si la propiedad es > 9, la mandamos a la 1 para que no rompa la FK
                    $idPropiedad = $idPropiedad > 9 ? 1 : $idPropiedad;

                    DB::table('comentarios_propiedad')->insert([
                        'idComentario' => trim($valArray[0]),
                        'idPropiedad'  => $idPropiedad,
                        'docUsuario'   => str_replace("'", "", trim($valArray[2])),
                        'comentario'   => str_replace("'", "", trim($valArray[3])),
                        'puntuacion'   => trim($valArray[4]),
                        'fecha'        => str_replace("'", "", trim($valArray[5])),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error en ComentariosPropiedadSeeder: " . $e->getMessage());
        }
    }
}