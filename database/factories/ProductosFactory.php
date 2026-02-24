<?php

namespace Database\Factories;

use App\Models\Productos;
use App\Models\UnidadDeMedidas;
use App\Models\CategoriaProducto;
use App\Models\SubcategoriaProducto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductosFactory extends Factory
{
    protected $model = Productos::class;

    protected $productosDerivadosDeLeche = [
        'Leche Entera' => 'Leches',
        'Leche Descremada' => 'Leches',
        'Queso Fresco' => 'Quesos',
        'Queso Cheddar' => 'Quesos',
        'Yogur Natural' => 'Yogures',
        'Yogur Griego' => 'Yogures',
        'Mantequilla' => 'Mantequillas',
        'Crema de Leche' => 'Cremas',
        'Leche Condensada' => 'Leches',
        'Leche Evaporada' => 'Leches',
        'Requesón' => 'Quesos',
        'Queso Cottage' => 'Quesos',
        'Helado de Vainilla' => 'Helados',
        'Leche de Cabra' => 'Leches',
        'Queso Mozzarella' => 'Quesos',
    ];

    protected $materiaPrima = [
        'Leche Cruda' => 'Ingredientes Base',
        'Cultivo Láctico' => 'Aditivos',
        'Cloruro de Calcio' => 'Aditivos',
        'Cuajo' => 'Aditivos',
        'Sal Industrial' => 'Condimentos',
    ];

    protected $insumos = [
        'Envase Plástico' => 'Envases',
        'Tapa Rosca' => 'Envases',
        'Etiqueta Adhesiva' => 'Etiquetado',
        'Bolsa Plástica' => 'Envases',
        'Detergente CIP' => 'Limpieza',
        'Desinfectante Ácido' => 'Limpieza',
    ];

    protected $equiposMaquinaria = [
        'Pasteurizadora' => 'Maquinaria',
        'Homogeneizadora' => 'Maquinaria',
        'Envasadora' => 'Maquinaria',
        'Tanque de Almacenamiento' => 'Equipos',
        'Mesa Inoxidable' => 'Equipos',
        'Bomba Centrífuga' => 'Equipos',
    ];

    public function definition()
    {
        $categoriaNombre = $this->faker->randomElement(['Producto', 'Materia Prima', 'Insumo', 'Equipo']);
        $empresaId = 1; // Ajustar según el tenant
        $userId = User::inRandomOrder()->first()?->id ?? null; // Obtener un usuario existente o null

        // Obtener o crear una categoría
        $categoria = CategoriaProducto::firstOrCreate(
            ['nombre' => $categoriaNombre],
            ['created_by' => $userId, 'updated_by' => $userId]
        );

        switch ($categoriaNombre) {
            case 'Materia Prima':
                $nombreProducto = array_key_first($this->faker->randomElement(array_chunk($this->materiaPrima, 1, true)));
                $subcategoriaNombre = $this->materiaPrima[$nombreProducto];
                $descripcion = "Materia prima para producción: " . $nombreProducto;
                break;
            case 'Insumo':
                $nombreProducto = array_key_first($this->faker->randomElement(array_chunk($this->insumos, 1, true)));
                $subcategoriaNombre = $this->insumos[$nombreProducto];
                $descripcion = "Insumo utilizado en planta: " . $nombreProducto;
                break;
            case 'Equipo':
                $nombreProducto = array_key_first($this->faker->randomElement(array_chunk($this->equiposMaquinaria, 1, true)));
                $subcategoriaNombre = $this->equiposMaquinaria[$nombreProducto];
                $descripcion = "Equipo o maquinaria: " . $nombreProducto;
                break;
            default:
                $nombreProducto = array_key_first($this->faker->randomElement(array_chunk($this->productosDerivadosDeLeche, 1, true)));
                $subcategoriaNombre = $this->productosDerivadosDeLeche[$nombreProducto];
                $descripcion = "Producto lácteo: " . $nombreProducto;
                break;
        }

        // Obtener o crear una subcategoría
        $subcategoria = SubcategoriaProducto::firstOrCreate(
            ['nombre' => $subcategoriaNombre, 'categoria_id' => $categoria->id],
            ['created_by' => $userId, 'updated_by' => $userId]
        );

        return [
            'unidad_de_medida_id' => UnidadDeMedidas::inRandomOrder()->first()->id ?? UnidadDeMedidas::factory()->create()->id,
            'categoria_id' => $categoria->id,
            'subcategoria_id' => $subcategoria->id,
            'nombre' => $nombreProducto,
            'descripcion' => $descripcion,
            'descripcion_corta' => $nombreProducto,
            'sku' => strtoupper($this->faker->bothify('???-#####')),
            'codigo' => $this->faker->unique()->ean8(),
            'color' => $this->faker->safeColorName(),
            'isv' => $this->faker->randomFloat(2, 0, 0.15),
            'empresa_id' => $empresaId,
            'created_by' => $userId,
            'updated_by' => $userId,
            'deleted_by' => null,
        ];
    }
}