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
        Schema::create('sire_compras_auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('sire_compras')->onDelete('cascade'); // Relación con compras
            $table->string('cod_usu_regis'); // Código de usuario que registró
            $table->dateTime('fec_regis'); // Fecha de registro
            $table->string('cod_usu_modif'); // Código de usuario que modificó
            $table->dateTime('fec_modif'); // Fecha de modificación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_compras_auditorias');
    }
};
