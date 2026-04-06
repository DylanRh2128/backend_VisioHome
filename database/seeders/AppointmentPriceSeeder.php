<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prices = [
            'precio_cita_alta' => '80000',
            'precio_cita_media' => '50000',
            'precio_cita_baja' => '30000',
        ];

        foreach ($prices as $key => $value) {
            DB::table('configurations')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
