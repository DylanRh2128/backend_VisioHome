<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Propiedad;
use Illuminate\Support\Facades\Storage;

class CreatePropertyFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'propiedades:crear-carpetas {--id= : ID específico de la propiedad} {--all : Crear carpetas para todas las propiedades}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea carpetas organizadas en storage/app/public/properties/ para las propiedades';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->option('id');
        $all = $this->option('all');

        if (!$id && !$all) {
            $this->error('Debes especificar un --id={valor} o usar el flag --all');
            return;
        }

        if ($all) {
            $propiedades = Propiedad::all();
            if ($propiedades->isEmpty()) {
                $this->info('No hay propiedades registradas en la base de datos.');
                return;
            }

            $this->info('Creando carpetas para todas las propiedades...');
            foreach ($propiedades as $p) {
                $this->createFolder($p->idPropiedad);
            }
            $this->info('¡Proceso completado!');
        } else {
            if (Propiedad::where('idPropiedad', $id)->exists()) {
                $this->createFolder($id);
                $this->info("Carpeta para la propiedad ID: {$id} procesada.");
            } else {
                $this->error("La propiedad con ID: {$id} no existe en la base de datos.");
            }
        }
    }

    /**
     * Crea la carpeta si no existe.
     *
     * @param int|string $id
     * @return void
     */
    private function createFolder($id)
    {
        $directory = "properties/propiedad_{$id}";

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
            $this->line("<info>Creada:</info> storage/app/public/{$directory}");
        } else {
            $this->line("<comment>Saltada (ya existe):</comment> storage/app/public/{$directory}");
        }
    }
}
