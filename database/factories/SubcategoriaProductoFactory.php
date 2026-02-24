<?php

namespace Database\Factories;

use App\Models\SubcategoriaProducto;
use App\Models\CategoriaProducto;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubcategoriaProductoFactory extends Factory
{
    protected $model = SubcategoriaProducto::class;

    protected $subcategorias = [
        'Producto' => ['Leches', 'Quesos', 'Yogures', 'Mantequillas', 'Cremas', 'Helados'],
        'Materia Prima' => ['Ingredientes Base', 'Aditivos', 'Condimentos'],
        'Insumo' => ['Envases', 'Etiquetado', 'Limpieza'],
        'Equipo' => ['Maquinaria', 'Equipos'],
    ];

    public function definition()
    {
        $categoria = CategoriaProducto::inRandomOrder()->first() ?? CategoriaProducto::factory()->create();
        $nombreCategoria = $categoria->nombre;

        return [
            'nombre' => $this->faker->randomElement($this->subcategorias[$nombreCategoria] ?? ['General']),
            'categoria_id' => $categoria->id,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}