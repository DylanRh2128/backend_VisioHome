<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->string('docUsuario', 20);
                $table->foreign('docUsuario')->references('docUsuario')->on('usuarios')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('idCart');
                $table->unsignedBigInteger('idPropiedad');
                $table->integer('cantidad')->default(1);
                $table->foreign('idCart')->references('id')->on('carts')->onDelete('cascade');
                $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
