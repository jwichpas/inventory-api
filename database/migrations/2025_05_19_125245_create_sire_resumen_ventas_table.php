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
        Schema::create('sire_resumen_ventas', function (Blueprint $table) {
            $table->id();
            $table->string('num_ruc', 11); // Número de RUC (máximo 11 caracteres)
            $table->unsignedSmallInteger('anio'); // Año (ejemplo: 2023)
            $table->unsignedTinyInteger('mes'); // Mes (1 a 12)
            $table->decimal('total_ventas', 12, 2); // Total de ventas (decimal con 12 dígitos y 2 decimales)
            $table->timestamps(); // Columnas created_at y updated_at
            // Índices para mejorar las consultas
            $table->index(['num_ruc', 'anio', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_resumen_ventas');
    }
};
