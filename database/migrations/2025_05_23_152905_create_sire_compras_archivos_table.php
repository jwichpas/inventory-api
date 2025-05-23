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
        Schema::create('sire_compras_archivos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('compra_id');
            $table->string('xml')->nullable();
            $table->string('cdr')->nullable();
            $table->string('pdf')->nullable();
            $table->string('guia')->nullable();
            $table->timestamps();

            $table->foreign('compra_id')->references('id')->on('sire_compras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_compras_archivos');
    }
};
