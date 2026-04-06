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
            if (!Schema::hasColumn('usuarios', 'genero')) {
                $table->enum('genero', ['Hombre', 'Mujer', 'Otro'])->default('Otro')->after('idRol');
            }
            if (!Schema::hasColumn('usuarios', 'login_count')) {
                $table->integer('login_count')->default(0)->after('genero');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['genero', 'login_count']);
        });
    }
};
