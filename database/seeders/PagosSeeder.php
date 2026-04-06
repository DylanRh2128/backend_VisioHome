<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PagosSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path("sql/visiohome/visiohome_pagos.sql");
        if (!File::exists($path)) return;

        $sql = File::get($path);
        $sql = str_replace('`', '"', $sql);
        preg_match_all('/INSERT INTO "pagos" VALUES \((.+?)\);/is', $sql, $matches);

        foreach ($matches[1] as $line) {
            $val = array_map(function($item) {
                $item = trim($item);
                return $item === 'NULL' ? null : str_replace("'", "", $item);
            }, explode(',', $line));

            $user = DB::table('usuarios')->where('docUsuario', $val[1])->first() ?? DB::table('usuarios')->first();

            DB::table('pagos')->insert([
                'idPago'      => $val[0],
                'docUsuario'  => $user->docUsuario,
                'idPropiedad' => (int)$val[2] > 9 ? 1 : $val[2],
                'idCita'      => $val[3],
                'monto'       => $val[4],
                'metodoPago'  => $val[5],
                'estado'      => $val[6],
                'referencia'  => $val[7],
                'fecha'       => $val[8],
            ]);
        }
    }
}