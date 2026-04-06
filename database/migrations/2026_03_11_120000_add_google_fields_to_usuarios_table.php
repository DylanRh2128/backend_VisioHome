<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'google_id')) {
                $table->string('google_id', 255)->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('usuarios', 'email_verified')) {
                $table->timestamp('email_verified')->nullable()->after('google_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'email_verified']);
        });
    }
};
