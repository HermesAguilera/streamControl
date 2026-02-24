<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Departamento;
use App\Models\Paises; 
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Departamento>
 */
class DepartamentoFactory extends Factory
{
    protected $model = Departamento::class;

    /**
     * Define the model's default state.
     *
     * La fábrica se simplifica. Los valores para 'nombre_departamento' y 'pais_id'
     * serán provistos directamente por el seeder.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'nombre_departamento' ya no se genera aquí, el Seeder lo proveerá.
            // 'pais_id' ya no se genera aquí, el Seeder lo proveerá.
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
