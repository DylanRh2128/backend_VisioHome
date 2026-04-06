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

        $sql = File::get($path);
        $sql = str_replace('`', '"', $sql);
        preg_match_all('/INSERT INTO "citas" VALUES \((.+?)\);/is', $sql, $matches);

        foreach ($matches[1] as $line) {
            $val = array_map(function($item) {
                $item = trim($item);
                return $item === 'NULL' ? null : str_replace("'", "", $item);
            }, explode(',', $line));

            $user = DB::table('usuarios')->where('docUsuario', $val[2])->first() 
                    ?? DB::table('usuarios')->first();

            if ($user) {
                // Buscamos el agente específico, o cualquier agente, o el mismo usuario si no hay más
                $agente = DB::table('usuarios')->where('docUsuario', $val[3])->first() 
                          ?? DB::table('usuarios')->where('rol', 'agente')->first() 
                          ?? $user;

                DB::table('citas')->insert([
                    'idCita'           => $val[0],
                    'idPropiedad'      => (int)$val[1] > 9 ? 1 : $val[1],
                    'docUsuario'       => $user->docUsuario,
                    'docAgente'        => $agente->docUsuario,
                    'fecha'            => $val[4],
                    'estado'           => $val[5],
                    'canal'            => $val[6],
                    'notas'            => $val[7],
                    'creado_en'        => $val[8] ?? now(),
                    'idDisponibilidad' => $val[9],
                    'precio'           => $val[10],
                ]);
            }
        }
    }
}