<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propiedad_imagenes', function (Blueprint $table) {
            $table->enum('tipo', ['imagen', 'modelo3d'])->default('imagen')->after('orden');
        });
    }

    public function down(): void
    {
        Schema::table('propiedad_imagenes', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
