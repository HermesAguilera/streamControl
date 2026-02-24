<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrdenComprasInsumos;
use App\Models\Proveedores;
use App\Models\Empresa;

class OrdenComprasInsumosFactory extends Factory
{
    protected $model = OrdenComprasInsumos::class;

    public function definition(): array
    {
        return [
            'proveedor_id' => Proveedores::factory(),
            'empresa_id' => Empresa::factory(),
            'fecha_realizada' => $this->faker->date(),
            'estado' => $this->faker->randomElement(['Pendiente', 'Recibida']),
            'descripcion' => $this->faker->optional()->paragraph,
            'created_by' => $this->faker->numberBetween(1, 10),
            'updated_by' => $this->faker->numberBetween(1, 10),
            'deleted_by' => null,
        ];
    }
}