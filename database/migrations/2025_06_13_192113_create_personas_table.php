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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('primer_nombre', 50);
            $table->string('segundo_nombre', 50)->nullable(); // Opcional
            $table->string('primer_apellido', 50);
            $table->string('segundo_apellido', 50)->nullable(); // Opcional
            $table->string('dni', 20)->unique(); 
            $table->enum('tipo_persona', ['natural', 'juridica'])->default('natural');
            $table->text('direccion');
            // Claves foráneas
            $table->foreignId('municipio_id')->constrained('municipios');
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos');
            $table->foreignId('pais_id')->constrained('paises')->nullable();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->string('telefono', 20)->nullable(); // Opcional
            $table->enum('sexo', ['MASCULINO', 'FEMENINO', 'OTRO']);
            $table->date('fecha_nacimiento'); // CAMBIO: Usamos 'date' para la fecha de nacimiento
            $table->string('fotografia', 255)->nullable(); 
            // Campos de auditoría (logs)
            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
