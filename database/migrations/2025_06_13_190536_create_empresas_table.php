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
            $table->string('nombre', 255);
            $table->foreignId('pais_id')->constrained('paises')->nullable(); 
            $table->foreignId('departamento_id')->constrained('departamentos')->nullable();            
            $table->foreignId('municipio_id')
                  ->constrained('municipios')
                  ->onDelete('cascade');
            $table->text('direccion', 200); 
            $table->string('telefono', 20)->nullable(); 
            $table->string('rtn', 20)->unique(); 
            $table->json('fotos')->nullable();
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
        Schema::dropIfExists('empresas');
    }
};
