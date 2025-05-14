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
        Schema::create('almacen_stock', function (Blueprint $table) {
            $table->foreignId('id_variante')->constrained('variante_products');
            $table->foreignId('id_almacen')->constrained('almacenes');
            $table->foreignId('id_lote')->constrained('lotes');
            $table->decimal('stock', 10, 5);
            $table->primary(['id_variante', 'id_almacen', 'id_lote']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almacen_stock');
    }
};
