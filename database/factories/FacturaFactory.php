<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\factura>
 */
class FacturaFactory extends Factory
{
    protected $model = Factura::class;

    public function definition()
    {
        return [
            'fecha_factura' => $this->faker->date(),
            'empresa_id' => \App\Models\Empresa::factory(),
            'user_id' => \App\Models\User::factory(),
            'cliente_id' => \App\Models\Cliente::factory(),
        ];
    }
}
