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
        Schema::create('empresa_tributos_sunat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->string('periodo', 6);
            $table->string('cod_tributo', 6);
            $table->dateTime('fec_vigencia');
            $table->dateTime('fec_alta');
            $table->string('cod_sis_pag', 10);
            $table->string('cod_fre_pago', 10);
            $table->string('cod_per_vsp', 10);
            $table->decimal('mto_imp_min', 10, 2);
            $table->string('cod_ges_min', 10);
            $table->string('ind_alta', 1);
            $table->string('cod_tip_ins', 1);
            $table->string('des_con_dis', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_tributos_sunat');
    }
};
