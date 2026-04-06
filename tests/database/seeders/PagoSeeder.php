<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagoSeeder extends Seeder
{
    public function run()
    {
        // Primero obtenemos IDs de propiedades y usuarios existentes
        $propiedades = DB::table('propiedades')->pluck('idPropiedad')->toArray();
        $usuarios = DB::table('usuarios')->pluck('docUsuario')->toArray();

        if (empty($propiedades) || empty($usuarios)) {
            return; // No crear pagos si no hay propiedades o usuarios
        }

        DB::table('pagos')->insertOrIgnore([
            [
                'docUsuario' => $usuarios[0] ?? '1033183120',
                'idPropiedad' => $propiedades[0] ?? 1,
                'idCita' => null,
                'monto' => 1500000.00,
                'metodoPago' => 'transferencia',
                'estado' => 'aprobado',
                'referencia' => 'FAC-001-2025',
                'fecha' => now()->subDays(15)
            ],
            [
                'docUsuario' => $usuarios[1] ?? '1038263414',
                'idPropiedad' => $propiedades[1] ?? 2,
                'idCita' => null,
                'monto' => 2300000.00,
                'metodoPago' => 'tarjeta',
                'estado' => 'pendiente',
                'referencia' => 'FAC-002-2025',
                'fecha' => now()->subDays(5)
            ],
            [
                'docUsuario' => $usuarios[0] ?? '1033183120',
                'idPropiedad' => $propiedades[2] ?? 3,
                'idCita' => null,
                'monto' => 890000.00,
                'metodoPago' => 'efectivo',
                'estado' => 'rechazado',
                'referencia' => 'FAC-003-2025',
                'fecha' => now()->subDays(2)
            ]
        ]);
    }
}
