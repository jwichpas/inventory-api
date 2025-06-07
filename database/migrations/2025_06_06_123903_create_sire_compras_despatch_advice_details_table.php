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
        Schema::create('sire_compras_despatch_advice_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_compra');
            $table->string('ItemIdentification', 50);
            $table->string('ItemDescription', 255);
            $table->string('DespatchLine', 20);
            $table->decimal('DeliveredQuantity', 12, 2);
            $table->string('unitCode', 10);
            $table->decimal('GrossWeightMeasure', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('id_compra')
                ->references('id')
                ->on('sire_compras_despatch_advice')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_compras_despatch_advice_details');
    }
};
