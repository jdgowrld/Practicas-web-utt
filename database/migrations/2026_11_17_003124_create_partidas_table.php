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
    Schema::create('partidas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('jugador_x_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('jugador_o_id')->nullable()->constrained('users')->onDelete('cascade');
        $table->foreignId('turno_user_id')->nullable()->constrained('users')->onDelete('set null');
        $table->foreignId('ganador_id')->nullable()->constrained('users')->onDelete('set null');
        $table->json('tablero'); // Arreglo de 9 posiciones: null, 'X', 'O'
        $table->enum('estado', ['Esperando', 'EnCurso', 'Finalizada'])->default('Esperando');
        $table->boolean('es_empate')->default(false);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
