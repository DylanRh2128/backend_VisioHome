<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('valoraciones_agentes');
        Schema::create('valoraciones_agentes', function (Blueprint $table) {
            $table->bigIncrements('idValoracion');
            $table->string('docAgente', 36);
            $table->string('docUsuario', 36);
            $table->integer('puntuacion');
            $table->text('comentario')->nullable();
            $table->timestamp('creado_en')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }
    public function down(): void {
        Schema::dropIfExists('valoraciones_agentes');
    }
};


