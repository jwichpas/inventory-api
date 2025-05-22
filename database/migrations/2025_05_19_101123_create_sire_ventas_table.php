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
        Schema::create('sire_ventas', function (Blueprint $table) {
            $table->id();
            $table->string('id_externo')->unique(); // ID externo del JSON
            $table->string('num_ruc', 11); // Número de RUC
            $table->string('nom_razon_social'); // Nombre o razón social
            $table->string('per_periodo_tributario', 6); // Período tributario (formato YYYYMM)
            $table->string('cod_car'); // Código de cartera
            $table->string('cod_tipo_cdp', 2); // Código de tipo de comprobante
            $table->string('num_serie_cdp'); // Número de serie del comprobante
            $table->string('num_cdp'); // Número del comprobante
            $table->string('cod_tipo_carga', 1); // Código de tipo de carga
            $table->string('cod_situacion', 1); // Código de situación
            $table->date('fec_emision'); // Fecha de emisión
            $table->string('cod_tipo_doc_identidad'); // Código de tipo de documento de identidad
            $table->string('num_doc_identidad'); // Número de documento de identidad
            $table->string('nom_razon_social_cliente'); // Nombre o razón social del cliente
            $table->decimal('mto_val_fact_expo', 12, 2); // Monto valor factura de exportación
            $table->decimal('mto_bi_gravada', 12, 2); // Monto base imponible gravada
            $table->decimal('mto_dscto_bi', 12, 2); // Monto descuento base imponible
            $table->decimal('mto_igv', 12, 2); // Monto IGV
            $table->decimal('mto_dscto_igv', 12, 2); // Monto descuento IGV
            $table->decimal('mto_exonerado', 12, 2); // Monto exonerado
            $table->decimal('mto_inafecto', 12, 2); // Monto inafecto
            $table->decimal('mto_isc', 12, 2); // Monto ISC
            $table->decimal('mto_bi_ivap', 12, 2); // Monto base imponible IVAP
            $table->decimal('mto_ivap', 12, 2); // Monto IVAP
            $table->decimal('mto_icbp', 12, 2); // Monto ICBP
            $table->decimal('mto_otros_trib', 12, 2); // Monto otros tributos
            $table->decimal('mto_total_cp', 12, 2); // Monto total del comprobante
            $table->string('cod_moneda', 3); // Código de moneda
            $table->decimal('mto_tipo_cambio', 12, 2); // Monto tipo de cambio
            $table->string('cod_estado_comprobante', 1); // Código de estado del comprobante
            $table->string('des_estado_comprobante'); // Descripción del estado del comprobante
            $table->string('ind_oper_gratuita', 1); // Indicador de operación gratuita
            $table->string('ind_tipo_operacion', 4)->nullable(); // Indicador de tipo de operacion
            $table->decimal('mto_valor_op_gratuitas', 12, 2); // Monto valor operaciones gratuitas
            $table->decimal('mto_valor_fob', 12, 2); // Monto valor FOB
            $table->decimal('mto_porc_participacion', 5, 2); // Monto porcentaje de participación
            $table->decimal('mto_valor_fob_dolar', 12, 2); // Monto valor FOB en dólares
            $table->integer('num_Inconsistencias')->nullable(); // Indicador de tipo de operación
            $table->string('semaforo')->nullable(); // Indicador de tipo de operación
            $table->json('lis_cod_Inconsistencia')->nullable(); // Documentos modificatorios (JSON)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_ventas');
    }
};
