<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('plataformas')) {
            Schema::create('plataformas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->text('descripcion')->nullable();
                $table->boolean('activa')->default(true);
                $table->unsignedTinyInteger('perfiles_por_cuenta')->default(5);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cuentas')) {
            Schema::create('cuentas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('plataforma_id')->constrained('plataformas')->cascadeOnDelete();
                $table->string('proveedor', 120);
                $table->string('correo', 255);
                $table->string('contrasena', 255);
                $table->date('fecha_inicio');
                $table->date('fecha_corte');
                $table->timestamps();

                $table->unique(['plataforma_id', 'correo'], 'cuentas_plataforma_correo_unique');
            });
        }

        if (! Schema::hasTable('perfiles')) {
            Schema::create('perfiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('plataforma_id')->constrained('plataformas')->cascadeOnDelete();
                $table->foreignId('cuenta_id')->nullable()->constrained('cuentas')->nullOnDelete();
                $table->string('nombre_perfil');
                $table->string('pin', 20)->nullable();
                $table->string('cliente_nombre')->nullable();
                $table->string('cliente_telefono', 30)->nullable();
                $table->string('proveedor_nombre')->nullable();
                $table->string('correo_cuenta')->nullable();
                $table->string('contrasena_cuenta')->nullable();
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_corte')->nullable();
                $table->date('fecha_caducidad_cuenta')->nullable();
                $table->timestamps();

                $table->unique(['plataforma_id', 'correo_cuenta', 'nombre_perfil'], 'perfiles_plataforma_correo_nombre_unique');
            });
        }

        if (! Schema::hasTable('cuenta_perfiles')) {
            Schema::create('cuenta_perfiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cuenta_id')->constrained('cuentas')->cascadeOnDelete();
                $table->unsignedInteger('numero_perfil');
                $table->string('pin', 20)->nullable();
                $table->timestamps();

                $table->unique(['cuenta_id', 'numero_perfil'], 'cuenta_perfiles_cuenta_numero_unique');
            });
        }

        if (! Schema::hasTable('cuentas_reportadas')) {
            Schema::create('cuentas_reportadas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('perfil_id')->nullable()->constrained('perfiles')->nullOnDelete();
                $table->foreignId('cuenta_id')->nullable()->constrained('cuentas')->nullOnDelete();
                $table->foreignId('plataforma_id')->nullable()->constrained('plataformas')->nullOnDelete();
                $table->foreignId('reportado_por')->nullable()->constrained('users')->nullOnDelete();
                $table->string('cuenta', 255);
                $table->string('numero_perfil', 100)->nullable();
                $table->text('descripcion');
                $table->string('estado', 30)->default('en_proceso');
                $table->timestamp('solucionado_at')->nullable();
                $table->timestamps();

                $table->index(['estado', 'solucionado_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_reportadas');
        Schema::dropIfExists('cuenta_perfiles');
        Schema::dropIfExists('perfiles');
        Schema::dropIfExists('cuentas');
        Schema::dropIfExists('plataformas');
    }
};
