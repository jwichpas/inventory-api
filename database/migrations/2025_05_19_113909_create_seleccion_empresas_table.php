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
        Schema::create('seleccion_empresas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id'); // Clave foránea a la tabla empresas
            $table->unsignedBigInteger('user_id')->nullable(); // Opcional: ID del usuario
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seleccion_empresas');
    }
};
