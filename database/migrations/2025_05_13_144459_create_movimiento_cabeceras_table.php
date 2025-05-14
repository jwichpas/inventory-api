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
        Schema::create('movimiento_cabecera', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa')->constrained('empresas');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('codigo_anexo',1)->nullable();
            $table->foreignId('id_proveedor')->nullable()->constrained('anexos');
            $table->string('id_tipo_invoice', 10);
            $table->string('serie', 10);
            $table->string('numero', 20);
            $table->string('moneda', 3);
            $table->decimal('tipo_cambio', 10, 4);
            $table->decimal('valor_compra', 12, 2);
            $table->decimal('gratuito', 12, 2);
            $table->decimal('igv', 12, 2);
            $table->decimal('total', 12, 2);
            $table->decimal('total_moneda_base', 12, 2);
            $table->string('id_tipo_operacion', 2);
            $table->string('id_tipo_operacion_fe', 2);
            $table->string('periodo', 7);
            $table->string('estado', 20);
            $table->date('fecha_recepcion')->nullable();
            $table->enum('tipo_movimiento', ['d', 'h']);
            $table->boolean('flete')->default(false);
            $table->integer('id_flete')->nullable();
            $table->boolean('letra')->default(false);
            $table->integer('id_letra')->nullable();
            $table->enum('forma_pago', ['CONTADO', 'CREDITO']);
            $table->timestamps();

            $table->foreign('id_tipo_invoice')->references('codigo')->on('tipo_documento');
            $table->foreign('id_tipo_operacion')->references('codigo')->on('tipo_operacion_ple');
            $table->foreign('id_tipo_operacion_fe')->references('codigo')->on('tipo_operacion');
            $table->foreign(['id_empresa', 'codigo_anexo'])->references(['id_empresa', 'codigo'])->on('tipo_anexos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_cabecera');
    }
};
