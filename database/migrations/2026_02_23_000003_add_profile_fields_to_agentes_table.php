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
        Schema::table('agentes', function (Blueprint $table) {
            if (!Schema::hasColumn('agentes', 'biografia')) {
                $table->text('biografia')->nullable()->after('especialidad');
            }
            if (!Schema::hasColumn('agentes', 'foto_perfil')) {
                $table->string('foto_perfil')->nullable()->after('correo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agentes', function (Blueprint $table) {
            $table->dropColumn(['biografia', 'foto_perfil']);
        });
    }
};
