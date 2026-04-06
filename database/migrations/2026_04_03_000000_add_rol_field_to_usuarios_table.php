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
        // 1. Añadir columna 'rol'
        try {
            Schema::table('usuarios', function (Blueprint $table) {
                if (!Schema::hasColumn('usuarios', 'rol')) {
                    $table->string('rol', 20)->after('idRol')->default('cliente');
                }
            });
        } catch (\Exception $e) {
            // Si falla por duplicado o similar, lo ignoramos y seguimos con los datos
        }

        // 2. Migrar datos de idRol a rol
        // 1 -> admin, 2 -> cliente, 3 -> agente
        DB::table('usuarios')->where('idRol', 1)->update(['rol' => 'admin']);
        DB::table('usuarios')->where('idRol', 2)->update(['rol' => 'cliente']);
        DB::table('usuarios')->where('idRol', 3)->update(['rol' => 'agente']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'rol')) {
                $table->dropColumn('rol');
            }
        });
    }
};
