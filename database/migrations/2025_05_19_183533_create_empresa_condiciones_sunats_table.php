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
        Schema::create('empresa_condiciones_sunat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->string('periodo', 6);
            $table->string('cod_dom_habido', 2);
            $table->string('cod_estado', 2);
            $table->dateTime('fec_alta');
            $table->string('cod_doble', 2);
            $table->string('cod_mclase', 2);
            $table->string('cod_reacti', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_condiciones_sunat');
    }
};
