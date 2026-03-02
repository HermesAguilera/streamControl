<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plataforma_id')->constrained('plataformas')->cascadeOnDelete();
            $table->string('nombre_perfil');
            $table->string('pin', 20)->nullable();
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_telefono', 30)->nullable();
            $table->string('proveedor_nombre')->nullable();
            $table->string('correo_cuenta')->nullable();
            $table->string('contrasena_cuenta')->nullable();
            $table->string('cliente_email')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_corte')->nullable();
            $table->date('fecha_caducidad_cuenta')->nullable();
            $table->timestamps();

            $table->unique(['plataforma_id', 'correo_cuenta', 'nombre_perfil'], 'perfiles_plataforma_correo_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfiles');
    }
};
