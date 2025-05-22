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
        Schema::create('sire_ventas_doc_modificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('sire_ventas')->onDelete('cascade'); // Clave foránea
            $table->date('fec_emision_mod'); // Fecha de emisión del documento modificatorio
            $table->string('cod_tipo_cdp_mod', 2); // Código de tipo de comprobante modificatorio
            $table->string('num_serie_cdp_mod'); // Número de serie del comprobante modificatorio
            $table->string('num_cdp_mod'); // Número del comprobante modificatorio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_ventas_doc_modificados');
    }
};
