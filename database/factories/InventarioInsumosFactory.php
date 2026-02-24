<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Empresa;
use App\Models\Productos;

class InventarioInsumosFactory extends Factory
{
    protected $model = \App\Models\InventarioInsumos::class;

    public function definition(): array
    {
        return [
            'empresa_id' => Empresa::factory(),
            'producto_id' => Productos::factory(),
            'cantidad' => $this->faker->numberBetween(1, 1000),
            'precio_costo' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}