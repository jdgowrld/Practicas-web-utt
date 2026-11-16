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
    Schema::create('equipos', function (Blueprint $table) {
        $table->id();
        $table->string('codigo')->unique(); // Ej. PC-LAB1-01
        $table->string('descripcion');      // Ej. Dell OptiPlex i7 / 16GB RAM
        $table->enum('estado', ['Disponible', 'Ocupado', 'Mantenimiento'])->default('Disponible');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
