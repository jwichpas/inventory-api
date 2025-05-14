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
        Schema::create('atributos_variante', function (Blueprint $table) {
            $table->foreignId('id_variante')->constrained('variante_products');
            $table->foreignId('id_atributo')->constrained('atributos');
            $table->primary(['id_variante', 'id_atributo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atributos_variante');
    }
};
