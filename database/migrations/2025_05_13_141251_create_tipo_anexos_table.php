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
        Schema::create('tipo_anexos', function (Blueprint $table) {
            $table->foreignId('id_empresa')->constrained('empresas');
            $table->string('codigo', 1);
            $table->string('name', 50);
            $table->primary(['id_empresa', 'codigo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_anexos');
    }
};
