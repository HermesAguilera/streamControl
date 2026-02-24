<?php

namespace Database\Factories;

use App\Models\OrdenCompras;
use App\Models\TipoOrdenCompras;
use App\Models\Proveedores;
use App\Models\Empresa;
use App\Models\Productos;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdenComprasFactory extends Factory
{
    protected $model = OrdenCompras::class;

    public function definition()
    {
        // Obtener un producto aleatorio o null
        $producto = Productos::inRandomOrder()->first();

        return [
            'tipo_orden_compra_id' => TipoOrdenCompras::inRandomOrder()->first()->id ?? TipoOrdenCompras::factory(),
            'proveedor_id' => Proveedores::inRandomOrder()->first()->id ?? Proveedores::factory(),
            'empresa_id' => Empresa::inRandomOrder()->first()->id ?? Empresa::factory(),
            'fecha_realizada' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'estado' => $this->faker->randomElement(['Pendiente', 'Recibida']),
            'descripcion' => $this->faker->optional()->sentence(),
            'created_by' => 1, // Ajustar según usuarios existentes
            'updated_by' => 1,
            'deleted_by' => null,
        ];
    }
}