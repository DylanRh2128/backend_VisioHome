<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FavoritesSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'favorites';
        $path = database_path("sql/visiohome/visiohome_{$table}.sql");

        if (!File::exists($path)) return;

        try {
            $sql = File::get($path);
            $sql = str_replace('`', '"', $sql);
            
            // Buscamos los valores del insert
            preg_match_all('/INSERT INTO "favorites" VALUES \((.+?)\);/is', $sql, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $values) {
                    $val = array_map(function($item) {
                        return trim($item) === 'NULL' ? null : str_replace("'", "", trim($item));
                    }, explode(',', $values));

                    // Validación de FK: Si la propiedad no existe (>9), usamos la 1
                                        // Dentro del foreach de FavoritesSeeder...
                    $user = DB::table('usuarios')->first();

                    DB::table('favorites')->insert([
                        'id'          => $val[0],
                        'docUsuario'  => $user->docUsuario, // Forzar usuario existente
                        'idPropiedad' => (int)$val[2] > 9 ? 1 : $val[2],
                        'created_at'  => $val[3] ?? now(),
                        'updated_at'  => $val[4] ?? now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error en FavoritesSeeder: " . $e->getMessage());
        }
    }
}