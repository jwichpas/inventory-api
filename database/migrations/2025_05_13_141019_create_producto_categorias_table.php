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
        Schema::create('producto_categoria', function (Blueprint $table) {
            $table->foreignId('id_producto')->constrained('products');
            $table->foreignId('id_categoria')->constrained('categories');
            $table->primary(['id_producto', 'id_categoria']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_categoria');
    }
};
