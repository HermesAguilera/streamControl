<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Paises; 
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paises>
 */
class PaisesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Paises::class; // Usa el nombre de clase correcto aquí, 'Paises'

    /**
     * Define the model's default state.
     *
     * En este caso, la fábrica ya no generará nombres de país aleatorios.
     * Simplemente se usará para crear instancias de Paises con los datos que le pasemos.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}

