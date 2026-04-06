<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('agentes')) {
            Schema::table('agentes', function (Blueprint $table) {
                if (!Schema::hasColumn('agentes', 'carrera')) {
                    $table->string('carrera', 100)->nullable()->after('nombre');
                }
                if (!Schema::hasColumn('agentes', 'especialidad')) {
                    $table->string('especialidad', 120)->nullable()->after('carrera');
                }
                if (!Schema::hasColumn('agentes', 'ciudad')) {
                    $table->string('ciudad', 80)->nullable()->after('direccion');
                }
                if (!Schema::hasColumn('agentes', 'experiencia_anos')) {
                    $table->integer('experiencia_anos')->default(0)->after('especialidad');
                }
                if (!Schema::hasColumn('agentes', 'promedio_valoracion')) {
                    $table->decimal('promedio_valoracion', 3, 2)->default(0.00)->after('activo');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
