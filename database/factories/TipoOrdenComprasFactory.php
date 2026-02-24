<?php

namespace Database\Factories;

use App\Models\TipoOrdenCompras;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoOrdenComprasFactory extends Factory
{
    protected $model = TipoOrdenCompras::class;

    public function definition()
    {
        $baseOrderTypes = [
            'Maquinaria',
            'Equipo',
            'Empaques',
        ];

        return [
            'nombre' => $this->faker->unique()->regexify('(' . implode('|', $baseOrderTypes) . ')[ -][A-Z0-9]{3,5}'),
            'empresa_id' => 1, // Adjust based on available empresa_id
            'created_by' => 1, // Adjust based on available users
            'updated_by' => 1,
        ];
    }
}