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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idCart')->constrained('carts')->onDelete('cascade');
            $table->unsignedBigInteger('idPropiedad'); // This might need to be 'string' if PK is string
            $table->integer('cantidad')->default(1);
            $table->timestamps();
            
            // We'll apply the foreign key in a separate step or ensure type matches
            // $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
