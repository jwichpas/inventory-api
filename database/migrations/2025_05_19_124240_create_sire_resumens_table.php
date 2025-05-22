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
        Schema::create('sire_resumens', function (Blueprint $table) {
            $table->id();
            $table->string('numRuc', 11);
            $table->string('nomRazonSocial');
            $table->string('perTributario', 4); // Año (ej: 2025)
            $table->integer('cntRegistrosPresentadosDP');
            $table->integer('cntRegistrosPresentadosFP');
            $table->integer('cntRegistrosPresentadosNG');
            $table->integer('cntRegistrosPresentados');
            $table->foreign('numRuc')->references('ruc')->on('empresas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_resumens');
    }
};
