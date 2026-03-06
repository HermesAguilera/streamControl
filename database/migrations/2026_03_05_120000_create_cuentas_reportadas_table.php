<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('cuentas_reportadas');
    }
};
