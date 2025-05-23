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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('ruc', 20)->unique();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('usuario_sol')->nullable();
            $table->string('clave_sol')->nullable();
            $table->string('cliente_id')->nullable();
            $table->string('cliente_secret')->nullable();
            $table->string('token')->nullable();
            $table->string('usuario_afp')->nullable();
            $table->string('clave_afp')->nullable();
            $table->string('imagen')->nullable();
            $table->string('estado')->default('1');
            $table->string('regimen_tributario')->nullable();
            $table->date('regimen_t_desde')->nullable();
            $table->string('regimen_laboral')->nullable();
            $table->string('regimen_l_desde')->nullable();
            $table->string('sunarp_oficina')->nullable();
            $table->string('sunarp_partida')->nullable();
            $table->string('sunarp_dni_representante')->nullable();
            $table->string('sunarp_nombre_representante')->nullable();
            $table->string('sunarp_cargo_representante')->nullable();
            $table->string('num_')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
