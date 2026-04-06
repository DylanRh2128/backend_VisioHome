<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ComentariosPropiedadSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path("sql/visiohome/visiohome_comentarios_propiedad.sql");
        if (!File::exists($path)) return;

        $sql = File::get($path);
        $sql = str_replace('`', '"', $sql);
        preg_match_all('/INSERT INTO "comentarios_propiedad" VALUES \((.+?)\);/is', $sql, $matches);

        foreach ($matches[1] as $line) {
            $val = array_map(function($item) {
                $item = trim($item);
                return $item === 'NULL' ? null : str_replace("'", "", $item);
            }, explode(',', $line));

            $user = DB::table('usuarios')->where('docUsuario', $val[2])->first() ?? DB::table('usuarios')->first();

            DB::table('comentarios_propiedad')->insert([
                'idComentario' => $val[0],
                'idPropiedad'  => (int)$val[1] > 9 ? 1 : $val[1],
                'docUsuario'   => $user->docUsuario,
                'comentario'   => $val[3],
                'puntuacion'   => $val[4],
                'fecha'        => $val[5],
            ]);
        }
    }
}