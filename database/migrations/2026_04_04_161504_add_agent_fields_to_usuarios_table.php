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
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'especialidad')) {
                $table->string('especialidad', 120)->nullable()->after('rol');
            }
            if (!Schema::hasColumn('usuarios', 'biografia')) {
                $table->text('biografia')->nullable()->after('especialidad');
            }
            if (!Schema::hasColumn('usuarios', 'carrera')) {
                $table->string('carrera', 150)->nullable()->after('biografia');
            }
            if (!Schema::hasColumn('usuarios', 'experiencia_anos')) {
                $table->integer('experiencia_anos')->default(0)->after('carrera');
            }
            if (!Schema::hasColumn('usuarios', 'nitInmobiliaria')) {
                $table->string('nitInmobiliaria', 50)->nullable()->after('experiencia_anos');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['especialidad', 'biografia', 'carrera', 'experiencia_anos', 'nitInmobiliaria']);
        });
    }
};
