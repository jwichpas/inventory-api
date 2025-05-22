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
        Schema::create('sire_resumen_detalles', function (Blueprint $table) {
            $table->id();
            $table->string('numRuc', 11);
            $table->integer('correlativo');
            $table->string('nomRazonSocial');
            $table->string('perTributario', 7); // Formato "YYYY-MM"
            $table->string('nomRegistro');
            $table->string('constancia');
            $table->string('nomArchivoConstanciaComprasPdf')->nullable();
            $table->string('nomArchivoConstanciaVentasPdf')->nullable();
            $table->date('fechGeneracion')->nullable();
            $table->date('fechVencimiento')->nullable();
            $table->string('codEstadoGeneracion');
            $table->string('desEstadoGeneracion');
            $table->string('perTributarioFormateado');
            $table->foreign('numRuc')->references('ruc')->on('empresas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_resumen_detalles');
    }
};
