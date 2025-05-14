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
        Schema::create('movimiento_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa')->constrained('empresas');
            $table->foreignId('id_cabecera')->constrained('movimiento_cabecera');
            $table->integer('secuencia');
            $table->foreignId('id_variante')->constrained('variante_products');
            $table->foreignId('id_lote')->nullable()->constrained('lotes');
            $table->decimal('cantidad', 12, 4);
            $table->decimal('valor_unitario', 12, 5);
            $table->decimal('precio_unitario', 12, 5);
            $table->decimal('valor_total', 12, 5);
            $table->decimal('precio_total', 12, 5);
            $table->string('id_tipo_precio_unitario', 10);
            $table->string('id_tipo_afectacion_igv', 10);
            $table->decimal('valor_unitario_final', 12, 5);
            $table->timestamps();

            $table->foreign('id_tipo_precio_unitario')->references('codigo')->on('tipo_precio_unitario');
            $table->foreign('id_tipo_afectacion_igv')->references('codigo')->on('tipo_afectacion_igv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_detalle');
    }
};
