<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PagosSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'pagos';
        $path = database_path("sql/visiohome/visiohome_{$table}.sql");

        if (!File::exists($path)) return;

        try {
            $sql = File::get($path);
            $sql = str_replace('`', '"', $sql);
            preg_match_all('/INSERT INTO "pagos" VALUES \((.+?)\);/is', $sql, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $values) {
                    $val = array_map(function($item) {
                        return trim($item) === 'NULL' ? null : str_replace("'", "", trim($item));
                    }, explode(',', $values));

                    $idProp = (int)$val[2] > 9 ? 1 : $val[2];

                    DB::table('pagos')->insert([
                        'idPago'      => $val[0],
                        'docUsuario'  => $val[1],
                        'idPropiedad' => $idProp,
                        'idCita'      => $val[3],
                        'monto'       => $val[4],
                        'metodoPago'  => $val[5],
                        'estado'      => $val[6],
                        'referencia'  => $val[7],
                        'fecha'       => $val[8],
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error en PagosSeeder: " . $e->getMessage());
        }
    }
}