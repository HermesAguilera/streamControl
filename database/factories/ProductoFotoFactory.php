<?php

namespace Database\Factories;

use App\Models\ProductoFoto;
use App\Models\Productos;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFotoFactory extends Factory
{
    protected $model = ProductoFoto::class;

    public function definition(): array
        {
            return [
                // No crear producto automáticamente aquí
                'producto_id' => null, // dejarlo como null por defecto
                'url' => 'productos/foto_' . $this->faker->uuid() . '.jpg',
            ];
        }

}