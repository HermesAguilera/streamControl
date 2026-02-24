<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Municipio;
use App\Models\Departamento; 

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Municipio>
 */
class MunicipioFactory extends Factory
{
    protected $model = Municipio::class;

    /**
     * Define the model's default state.
     *
     * La fábrica se simplifica. Los valores para 'nombre_municipio' y 'departamento_id'
     * serán provistos directamente por el seeder.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'nombre_municipio' y 'departamento_id' serán establecidos por el seeder.
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
