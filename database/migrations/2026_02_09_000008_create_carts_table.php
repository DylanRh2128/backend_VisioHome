<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('docUsuario', 20);
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idCart');
            $table->unsignedBigInteger('idPropiedad');
            $table->integer('cantidad')->default(1);
            $table->timestamps();

            $table->foreign('idCart')->references('id')->on('carts')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};


