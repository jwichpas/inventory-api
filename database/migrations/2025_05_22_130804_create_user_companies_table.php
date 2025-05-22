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
        Schema::create('user_companies', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id'); // `user_id INT`
            $table->unsignedBigInteger('empresa_id'); // `company_id INT`
            // Claves primarias compuestas
            $table->primary(['user_id', 'empresa_id']); // `PRIMARY KEY (user_id, company_id)
            // Claves foráneas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // `FOREIGN KEY (user_id) REFERENCES users(id)`
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade'); // `FOREIGN KEY (company_id) REFERENCES companies(id)`
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_companies');
    }
};
