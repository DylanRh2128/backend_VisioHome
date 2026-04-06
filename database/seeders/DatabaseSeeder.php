<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            UsuariosSeeder::class,
            InmobiliariasSeeder::class,
            AgentesSeeder::class,
            PropiedadesSeeder::class,
            PropiedadImagenesSeeder::class,
            DisponibilidadesSeeder::class, // Movido arriba de Citas
            ComentariosPropiedadSeeder::class,
            CommentsSeeder::class,
            CartsSeeder::class,
            CartItemsSeeder::class,
            CitasSeeder::class,
            PagosSeeder::class, // Usaremos solo PagosSeeder (el del SQL)
            ValoracionesAgentesSeeder::class,
            FavoritesSeeder::class,
            ConfigurationsSeeder::class,
        ]);

        $this->command->info('✅ Base de datos de VisioHome sembrada correctamente.');
    }
}