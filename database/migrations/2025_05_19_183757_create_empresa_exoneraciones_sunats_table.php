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
        Schema::create('empresa_exoneraciones_sunat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tributo_id')->constrained('empresa_tributos_sunat')->onDelete('cascade');
            $table->string('cod_exo_dis', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_exoneraciones_sunat');
    }
};
