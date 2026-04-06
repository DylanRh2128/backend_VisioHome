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
        $path = database_path("sql/visiohome/visiohome_citas.sql");
        if (!File::exists($path)) return;

        try {
            $sql = File::get($path);
            $sql = str_replace('`', '"', $sql);
            preg_match_all('/INSERT INTO "citas" VALUES \((.+?)\);/is', $sql, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $line) {
                    $val = array_map(function($item) {
                        $item = trim($item);
                        return $item === 'NULL' ? null : str_replace("'", "", $item);
                    }, explode(',', $line));

                    // Validación: Si el usuario no existe en la DB actual, usamos el primero que haya
                    $userExists = DB::table('usuarios')->where('docUsuario', $val[2])->exists();
                    $agenteExists = DB::table('usuarios')->where('docUsuario', $val[3])->exists();
                    
                    $docUser = $userExists ? $val[2] : DB::table('usuarios')->value('docUsuario');
                    $docAgente = $agenteExists ? $val[3] : DB::table('usuarios')->where('rol', 'agente')->value('docUsuario');

                    DB::table('citas')->insert([
                        'idCita'           => $val[0],
                        'idPropiedad'      => (int)$val[1] > 9 ? 1 : $val[1],
                        'docUsuario'       => $docUser,
                        'docAgente'        => $docAgente,
                        'fecha'            => $val[4],
                        'estado'           => $val[5],
                        'canal'            => $val[6],
                        'notas'            => $val[7],
                        'creado_en'        => $val[8], // Nombre exacto de tu tabla
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