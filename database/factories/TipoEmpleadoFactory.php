<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TipoEmpleado;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoEmpleado>
 */
class TipoEmpleadoFactory extends Factory
{
    protected $model = TipoEmpleado::class;

    /**
     * Define the model's default state.
     *
     * La fábrica se simplifica. Los valores para 'nombre_tipo' y 'descripcion'
     * serán provistos directamente por el seeder.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'nombre_tipo' y 'descripcion' serán establecidos por el seeder
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
