<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El Password Broker de Laravel siempre usa la columna 'email'
 * en la tabla password_reset_tokens. Renombramos 'correo' → 'email'
 * para compatibilidad. El valor almacenado sigue siendo el correo
 * del usuario gracias a Usuario::getEmailForPasswordReset().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Si la columna se llama 'correo', la renombramos a 'email'
            if (Schema::hasColumn('password_reset_tokens', 'correo') &&
                !Schema::hasColumn('password_reset_tokens', 'email')) {
                $table->renameColumn('correo', 'email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_tokens', 'email') &&
                !Schema::hasColumn('password_reset_tokens', 'correo')) {
                $table->renameColumn('email', 'correo');
            }
        });
    }
};
