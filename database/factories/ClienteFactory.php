<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Persona;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array // Define los campos que quieres generar para el modelo Cliente
    {
        return [
            'numero_cliente' => $this->faker->unique()->randomNumber(6),
            'rtn' => $this->faker->unique()->randomNumber(9),
            'persona_id' => Persona::factory(), // Asegúrate de tener un factory para Persona
            'empresa_id' => $this->faker->optional()->randomElement([Empresa::factory(), null]), // Genera null o un factory de Empresa
            'created_by' => 1, // ID de usuario de ejemplo
            'updated_by' => 1, // ID de usuario de ejemplo
            'deleted_by' => null, // Puede ser null si no está eliminado
        ];
    }
}