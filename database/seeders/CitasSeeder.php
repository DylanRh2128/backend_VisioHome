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
            // Quitamos las comillas de MySQL y ajustamos para Postgres
            $sql = str_replace('`', '"', $sql);
            
            // Eliminamos el INSERT INTO genérico y usamos uno con columnas explícitas
            // para asegurar que "creado_en" se llene correctamente.
            preg_match_all('/INSERT INTO "citas" VALUES \((.+?)\);/is', $sql, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $values) {
                    DB::unprepared("INSERT INTO \"citas\" (\"idCita\", \"idPropiedad\", \"docUsuario\", \"docAgente\", \"fecha\", \"estado\", \"canal\", \"notas\", \"creado_en\", \"idDisponibilidad\", \"precio\") VALUES ($values)");
                }
            }
        } catch (\Exception $e) {
            Log::error("Error en CitasSeeder: " . $e->getMessage());
        }
    }
}