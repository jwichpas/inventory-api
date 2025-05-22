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
        Schema::create('sire_periodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ejercicios_id')->constrained('sire_ejercicios')->onDelete('cascade'); // Relación con ejercicios
            $table->string('per_tributario', 6)->nullable(); // Campo para "numEjercicio"
            $table->string('cod_estado', 2)->nullable(); // Campo para "desEstado"
            $table->string('des_estado', 50)->nullable(); // Campo para "desEstado"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_periodos');
    }
};
