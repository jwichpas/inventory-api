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
        Schema::create('sire_compras_despatch_advice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_compra')->unique();
            $table->string('DespatchAdviceTypeCode', 20);
            $table->string('numero_guia', 50)->unique();
            $table->date('IssueDate');
            $table->time('IssueTime')->nullable();
            $table->string('HandlingCode', 10)->nullable();
            $table->text('HandlingInstructions')->nullable();
            $table->string('unitCode', 10)->nullable();
            $table->decimal('GrossWeightMeasure', 12, 2)->nullable();
            $table->string('TransportModeCode', 10)->nullable();
            $table->date('StartDate')->nullable();
            $table->string('DespatchSupplierPartyId', 50);
            $table->string('DespatchSupplierPartyName', 100);
            $table->string('DeliveryCustomerPartyId', 50);
            $table->string('DeliveryCustomerPartyName', 100);
            $table->string('CarrierPartyId', 50)->nullable();
            $table->string('CarrierPartyName', 100)->nullable();
            $table->string('DeliveryAddressId', 50)->nullable();
            $table->text('DeliveryAddressLine')->nullable();
            $table->string('DespatchAddressId', 50)->nullable();
            $table->text('DespatchAddressLine')->nullable();
            $table->text('xml_file')->nullable();
            $table->text('pdf_file')->nullable();
            $table->timestamps();

            $table->foreign('id_compra')
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
        Schema::dropIfExists('sire_compras_despatch_advice');
    }
};
