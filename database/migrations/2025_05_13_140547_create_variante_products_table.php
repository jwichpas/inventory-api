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
        Schema::create('variante_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_producto')->constrained('products');
            $table->string('sku', 100)->unique();
            $table->string('codigo_sunat', 13)->nullable()->unique();
            $table->string('ean13', 13)->nullable()->unique();
            $table->string('ean14', 14)->nullable()->unique();
            $table->string('imagen', 255)->nullable();
            $table->decimal('costo', 10, 3)->default(0);
            $table->decimal('precio', 10, 3)->default(0);
            $table->foreignId('id_unidad_medida')->constrained('unidad_medidas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variante_products');
    }
};
