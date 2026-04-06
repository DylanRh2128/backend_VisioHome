<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Store existing agents data
        $oldAgents = DB::table('agentes')->get();

        // 2. Drop referring foreign keys temporarily
        // Table: citas
        if (Schema::hasTable('citas')) {
            Schema::table('citas', function (Blueprint $table) {
                // Try dropping by name or the default convention
                try {
                    $table->dropForeign('fk_cita_agente');
                } catch (\Exception $e) {}
            });
        }
        
        // Table: valoraciones_agentes
        if (Schema::hasTable('valoraciones_agentes')) {
            Schema::table('valoraciones_agentes', function (Blueprint $table) {
                try {
                    $table->dropForeign('valoraciones_agentes_docagente_foreign');
                } catch (\Exception $e) {}
            });
        }

        // 3. Drop and recreate agents table with new structure
        Schema::dropIfExists('agentes');

        Schema::create('agentes', function (Blueprint $table) {
            $table->id();
            $table->string('idUsuario', 20); // Foreign key to docUsuario in usuarios
            $table->string('especialidad')->nullable();
            $table->string('telefono')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();

            $table->foreign('idUsuario')->references('docUsuario')->on('usuarios')->onDelete('cascade');
        });

        // 4. Migrate data and ensure agents exist as users
        foreach ($oldAgents as $agent) {
            $doc = $agent->docAgente;
            
            // Check if user already exists
            $userExists = DB::table('usuarios')->where('docUsuario', $doc)->exists();

            if (!$userExists) {
                // Create user for the agent
                DB::table('usuarios')->insert([
                    'docUsuario' => $doc,
                    'nombre' => $agent->nombre,
                    'correo' => $agent->correo,
                    'telefono' => $agent->telefono,
                    'direccion' => property_exists($agent, 'direccion') ? $agent->direccion : null,
                    'password' => bcrypt('password123'), // Default password
                    'idRol' => 3, // Agente
                    'creado_en' => now(),
                ]);
            } else {
                // Update role to agent
                DB::table('usuarios')
                    ->where('docUsuario', $doc)
                    ->update(['idRol' => 3]);
            }

            // Insert into new agents table
            DB::table('agentes')->insert([
                'idUsuario' => $doc,
                'especialidad' => property_exists($agent, 'especialidad') ? $agent->especialidad : null,
                'telefono' => $agent->telefono,
                'estado' => (isset($agent->activo) && $agent->activo) ? 'activo' : 'inactivo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Restore foreign keys pointing to docUsuario
        if (Schema::hasTable('citas')) {
            Schema::table('citas', function (Blueprint $table) {
                $table->foreign('docAgente')->references('docUsuario')->on('usuarios')->onDelete('set null');
            });
        }

        if (Schema::hasTable('valoraciones_agentes')) {
            Schema::table('valoraciones_agentes', function (Blueprint $table) {
                $table->foreign('docAgente')->references('docUsuario')->on('usuarios')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Simple drop for rollback
        Schema::dropIfExists('agentes');
    }
};
