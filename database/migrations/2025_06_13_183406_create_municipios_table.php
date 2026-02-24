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
        Schema::create('municipios', function (Blueprint $table) {
            $table->id(); 
            $table->string('nombre_municipio', 100); 
            $table->foreignId('departamento_id')
                ->constrained('departamentos')
                ->onDelete('cascade');
            $table->foreignId('pais_id')->nullable(); // Relación con país, nullable
            $table->timestamps(); 
            $table->softDeletes(); 
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
        Schema::dropIfExists('municipios');
    }
};
