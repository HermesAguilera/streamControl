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
            $table->string('cliente_documento', 50)->nullable();
            $table->string('cliente_direccion')->nullable();
            $table->enum('estado', ['disponible', 'activo', 'vencido', 'suspendido'])->default('disponible');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_corte')->nullable();
            $table->date('fecha_caducidad_cuenta')->nullable();
            $table->boolean('disponible')->default(true);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique(['plataforma_id', 'nombre_perfil']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfiles');
    }
};
