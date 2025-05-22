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
        Schema::create('sire_compras_tipo_cambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('sire_compras')->onDelete('cascade'); // Relación con compras
            $table->string('ind_carga_tipo_cambio'); // Indicador de carga de tipo de cambio
            $table->decimal('mto_cambio_moneda_extranjera', 10, 2)->nullable(); // Monto en moneda extranjera
            $table->decimal('mto_cambio_moneda_dolares', 10, 2)->nullable(); // Monto en dólares
            $table->decimal('mto_tipo_cambio', 10, 2)->nullable(); // Monto de tipo de cambio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_compras_tipo_cambios');
    }
};
