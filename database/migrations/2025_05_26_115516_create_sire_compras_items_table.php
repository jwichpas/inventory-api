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
        Schema::create('sire_compras_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('compra_id');
            $table->decimal('invoicedQuantity', 12, 2);
            $table->string('unitCode', 10);
            $table->decimal('priceAmount', 12, 2);
            $table->string('priceTypeCode', 10)->nullable();
            $table->string('sellersId', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('itemClassCode', 10)->nullable();
            $table->decimal('taxAmount', 12, 2)->nullable();
            $table->decimal('taxableAmount', 12, 2)->nullable();
            $table->string('taxExemptionReasonCode', 10)->nullable();
            $table->decimal('percent', 5, 2)->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('compra_id')
                ->references('id')
                ->on('sire_compras')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sire_compras_items');
    }
};
