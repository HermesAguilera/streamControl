<?php

namespace Database\Factories;

use App\Models\OrdenCompras;
use App\Models\Productos;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdenComprasDetalleFactory extends Factory
{
    public function definition()
    {
        return [
            'orden_compra_id' => OrdenCompras::inRandomOrder()->first()->id, // Usa una orden existente
            'producto_id' => Productos::inRandomOrder()->first()->id, // Usa un producto existente
            'cantidad' => $this->faker->numberBetween(1, 100),
            'precio' => $this->faker->randomFloat(2, 1, 1000),
            'created_by' => 1,
            'updated_by' => 1,
            'deleted_by' => null,
        ];
    }
}