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
        Schema::create('sire_compras', function (Blueprint $table) {
            $table->id();
            $table->string('id_registro')->unique(); // ID único del registro
            $table->string('num_ruc'); // Número de RUC
            $table->string('nom_razon_social'); // Nombre o razón social
            $table->string('cod_car'); // Código de carga
            $table->string('cod_tipo_cdp'); // Código de tipo de comprobante
            $table->string('des_tipo_cdp'); // Descripción del tipo de comprobante
            $table->string('num_serie_cdp'); // Número de serie del comprobante
            $table->string('num_cdp'); // Número del comprobante
            $table->date('fec_emision'); // Fecha de emisión
            $table->date('fec_venc_pag')->nullable(); // Fecha de vencimiento (puede ser nulo)
            $table->string('num_cdp_rango_final')->nullable(); // Número de CDP rango final (puede ser nulo)
            $table->string('cod_tipo_doc_identidad_proveedor'); // Código de tipo de documento del proveedor
            $table->string('num_doc_identidad_proveedor'); // Número de documento del proveedor
            $table->string('nom_razon_social_proveedor'); // Nombre o razón social del proveedor
            $table->string('cod_tipo_carga'); // Código de tipo de carga
            $table->string('cod_situacion'); // Código de situación
            $table->string('cod_moneda'); // Código de moneda
            $table->decimal('mto_total_cp', 10, 2); // Monto total del comprobante // AGREGADO
            $table->string('cod_estado_comprobante')->nullable(); // Código de estado del comprobante
            $table->string('des_estado_comprobante')->nullable(); // Descripción del estado del comprobante
            $table->string('ind_oper_gratuita')->nullable(); // Indicador de operación gratuita (puede ser nulo)
            $table->string('cod_tipo_motivo_nota')->nullable(); // Código de tipo de motivo de nota (puede ser nulo)
            $table->string('des_tipo_motivo_nota')->nullable(); // Descripción del tipo de motivo de nota (puede ser nulo)
            $table->string('ind_editable')->nullable(); // Indicador de editable (puede ser nulo)
            $table->string('per_tributario'); // Período tributario
            $table->integer('num_inconsistencias')->nullable(); // Número de inconsistencias (puede ser nulo)
            $table->string('ind_inf_incompleta')->nullable(); // Indicador de información incompleta (puede ser nulo)
            $table->string('ind_modificado_contribuyente')->nullable(); // Indicador de modificado por el contribuyente (puede ser nulo)
            $table->string('plazo_visualizacion')->nullable(); // Plazo de visualización (puede ser nulo)
            $table->string('ind_detraccion')->nullable(); // Indicador de detracción (puede ser nulo)
            $table->integer('ind_inclu_exclu_car'); // Indicador de incluido/excluido en el CAR
            $table->decimal('por_participacion', 5, 2)->nullable(); // Porcentaje de participación (puede ser nulo)
            $table->string('cod_bbss')->nullable(); // Código BBSS (puede ser nulo)
            $table->string('cod_id_proyecto')->nullable(); // Código de ID del proyecto (puede ser nulo)
            $table->string('ann_cdp')->nullable(); // Año del CDP (puede ser nulo)
            $table->string('cod_dep_aduanera')->nullable(); // Código de dependencia aduanera (puede ser nulo)
            $table->string('ind_fuente_cp')->nullable(); // Indicador de fuente CP
            $table->json('lis_cod_inconsistencia')->nullable(); // Lista de códigos de inconsistencia (puede ser nulo)
            $table->json('lis_num_casilla')->nullable(); // Lista de números de casilla (puede ser nulo)
            $table->decimal('por_tasa_retencion', 5, 2)->nullable(); // Porcentaje de tasa de retención (puede ser nulo)
            $table->string('des_msj_original')->nullable(); // Descripción del mensaje original (puede ser nulo)
            $table->string('num_car_ind_ie')->nullable(); // Número de CAR indicador IE (puede ser nulo)
            $table->integer('num_correlativo'); // Número correlativo
            $table->decimal('por_tasa_igv', 5, 2); // Porcentaje de tasa IGV
            $table->json('archivo_carga')->nullable(); // Archivo de carga (puede ser nulo)
            $table->json('campos_libres')->nullable(); // Campos libres (puede ser nulo)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_compras');
    }
};
