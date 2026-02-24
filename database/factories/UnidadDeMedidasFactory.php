<?php

namespace Database\Factories;

use App\Models\UnidadDeMedidas;
use App\Models\CategoriaUnidades;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadDeMedidasFactory extends Factory
{
    protected $model = UnidadDeMedidas::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->word(),
            'abreviacion' => strtoupper($this->faker->lexify('???')),
            'categoria_id' => CategoriaUnidades::factory(),
            'created_by' => 1,
            'updated_by' => 1,
            'deleted_by' => null,
        ];
    }
}
