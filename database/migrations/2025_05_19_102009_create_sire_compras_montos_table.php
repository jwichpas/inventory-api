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
        Schema::create('sire_compras_montos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('sire_compras')->onDelete('cascade'); // Relación con compras
            $table->decimal('mto_bi_gravada_dg', 10, 2); // mtoBIGravadaDG
            $table->decimal('mto_igv_ipm_dg', 10, 2); // mtoIgvIpmDG
            $table->decimal('mto_bi_gravada_dgng', 10, 2)->default(0); // mtoBIGravadaDGNG
            $table->decimal('mto_igv_ipm_dgng', 10, 2)->default(0); // mtoIgvIpmDGNG
            $table->decimal('mto_bi_gravada_dng', 10, 2)->default(0); // mtoBIGravadaDNG
            $table->decimal('mto_igv_ipm_dng', 10, 2)->default(0); // mtoIgvIpmDNG
            $table->decimal('mto_valor_adq_ng', 10, 2)->default(0); // mtoValorAdqNG
            $table->decimal('mto_icbp', 10, 2)->default(0); // mtoIcbp
            $table->decimal('mto_otros_trib', 10, 2)->default(0); // mtoOtrosTrib
            $table->decimal('mto_total_cp', 10, 2); // mtoTotalCp
            $table->decimal('mto_isc', 10, 2)->default(0); // mtoISC
            $table->decimal('mto_imb', 10, 2)->default(0); // mtoIMB
            $table->decimal('mto_bi_gravada_dg_original', 10, 2)->nullable(); // mtoBIGravadaDGOriginal
            $table->decimal('mto_igv_ipm_dg_original', 10, 2)->nullable(); // mtoIgvIpmDGOriginal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_compras_montos');
    }
};
