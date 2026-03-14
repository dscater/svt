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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string("codigo", 255)->unique();
            $table->string("nombre");
            $table->string("foto", 255)->nullable();
            $table->string("marca", 255)->nullable();
            $table->string("modelo", 255)->nullable();
            $table->decimal("precio", 24, 2)->nullable();
            $table->string("talla", 255)->nullable();
            $table->date("fecha_registro")->nullable();
            $table->time("hora_registro")->nullable();
            $table->integer("status")->default(1); // 0: eliminado, 1: activo, 2: vendido 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
