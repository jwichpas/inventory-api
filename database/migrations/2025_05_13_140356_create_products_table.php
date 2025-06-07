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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa')->constrained('empresas');
            $table->foreignId('id_brand')->constrained()->onDelete('cascade');
            $table->foreignId('id_category')->constrained()->onDelete('cascade');
            $table->foreignId('id_unidad_medida')->constrained()->onDelete('cascade');
            $table->string('codigo', 50);
            $table->string('name', 50);
            $table->string('description', 100)->nullable();
            $table->string('imagen', 255)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('stock',10,4)->default(0);
            $table->string('slug')->nullable();
            $table->string('ean13')->nullable();
            $table->string('ean14')->nullable();
            $table->string('codigo-sunat')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
