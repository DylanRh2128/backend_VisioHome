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
        Schema::table('disponibilidades', function (Blueprint $table) {
            if (!Schema::hasColumn('disponibilidades', 'estado')) {
                $table->enum('estado', ['disponible', 'reservada', 'comprada'])
                      ->default('disponible')
                      ->after('hora_fin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disponibilidades', function (Blueprint $table) {
            if (Schema::hasColumn('disponibilidades', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
