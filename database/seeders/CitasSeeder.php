<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CitasSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'citas';
        $path = database_path("sql/visiohome/visiohome_{$table}.sql");

        if (!File::exists($path)) return;

        try {
            $sql = File::get($path);
            $sql = str_replace('`', '"', $sql);
            preg_match_all('/INSERT INTO "citas" VALUES \((.+?)\);/is', $sql, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $values) {
                    $val = array_map(function($item) {
                        return trim($item) === 'NULL' ? null : str_replace("'", "", trim($item));
                    }, explode(',', $values));

                    $idProp = (int)$val[1] > 9 ? 1 : $val[1];

                    DB::table('citas')->insert([
                        'idCita'           => $val[0],
                        'idPropiedad'      => $idProp,
                        'docUsuario'       => $val[2],
                        'docAgente'        => $val[3],
                        'fecha'            => $val[4],
                        'estado'           => $val[5],
                        'canal'            => $val[6],
                        'notas'            => $val[7],
                        'creado_en'        => $val[8], // Aquí usamos el nombre exacto de tu migración
                        'idDisponibilidad' => $val[9],
                        'precio'           => $val[10],
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error en CitasSeeder: " . $e->getMessage());
        }
    }
}