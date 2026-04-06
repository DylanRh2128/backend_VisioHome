<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden de dependencias
        $this->call([
            UsuarioSeeder::class,           // Primero usuarios
            InmobiliariaSeeder::class,      // Luego inmobiliarias
            AgenteSeeder::class,            // Agentes (dependen de inmobiliarias)
            PropiedadSeeder::class,         // Propiedades (dependen de inmobiliarias)
            PagoSeeder::class,              // Pagos (dependen de usuarios y propiedades)
        ]);

        $this->command->info('✅ Datos de prueba cargados exitosamente');
    }
}
