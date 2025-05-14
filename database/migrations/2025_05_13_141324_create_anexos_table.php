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
        Schema::create('anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa')->constrained('empresas');
            /* $table->foreignId('codigo_tipo_anexo')->constrained('tipo_anexos'); */
            $table->string('tipo_anexo', 1);
            $table->foreignId('id_tipo_documento_identidad')->constrained('tipo_documento_identidad');
            $table->string('codigo', 1);
            $table->string('documento', 20);
            $table->string('nombre', 255);
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('contacto', 255)->nullable();
            $table->boolean('ag_retencion', 255)->nullable();
            $table->boolean('ag_percepcion', 255)->nullable();
            $table->boolean('buen_contribuyente', 255)->nullable();
            $table->boolean('estado', 1)->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anexos');
    }
};
