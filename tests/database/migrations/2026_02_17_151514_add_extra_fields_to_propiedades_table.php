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
        Schema::table('propiedades', function (Blueprint $table) {
            if (!Schema::hasColumn('propiedades', 'latitud')) {
                $table->decimal('latitud', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('propiedades', 'longitud')) {
                $table->decimal('longitud', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('propiedades', 'habitaciones')) {
                $table->integer('habitaciones')->default(1);
            }
            if (!Schema::hasColumn('propiedades', 'banos')) {
                $table->integer('banos')->default(1);
            }
            if (!Schema::hasColumn('propiedades', 'imagen_principal')) {
                $table->string('imagen_principal')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('propiedades', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud', 'habitaciones', 'banos', 'imagen_principal']);
        });
    }
};
