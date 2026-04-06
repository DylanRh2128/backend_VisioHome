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
        $this->call([
            RolesSeeder::class,
            UsuariosSeeder::class,
            InmobiliariasSeeder::class,
            AgentesSeeder::class,
            PropiedadesSeeder::class,
            PropiedadImagenesSeeder::class,
            ComentariosPropiedadSeeder::class,
            CommentsSeeder::class,
            CartsSeeder::class,
            CartItemsSeeder::class,
            CitasSeeder::class,
            PagosSeeder::class,
            DisponibilidadesSeeder::class,
            ValoracionesAgentesSeeder::class,
            FavoritesSeeder::class,
            ConfigurationsSeeder::class,
        ]);

        $this->command->info('✅ Base de datos sembrada exitosamente desde archivos SQL');
    }
}
