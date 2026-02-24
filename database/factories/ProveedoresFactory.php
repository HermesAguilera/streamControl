<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Proveedores;
use App\Models\Empresa;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proveedores>
 */
class ProveedoresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $empresa = \App\Models\Empresa::inRandomOrder()->first();
        return [
            'nombre_proveedor' => $this->faker->company,
            'telefono' => $this->faker->phoneNumber,
            'rtn' => $this->faker->numerify('##########'),
            'direccion' => $this->faker->address,
            'municipio_id' => 1, // o el que corresponda
            'persona_contacto' => $this->faker->name,
            'empresa_id' => $empresa ? $empresa->id : Empresa::factory()->create()->id,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
