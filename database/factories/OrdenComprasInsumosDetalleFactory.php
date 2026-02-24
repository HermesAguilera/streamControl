<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrdenComprasInsumosDetalle;
use App\Models\OrdenComprasInsumos;
use App\Models\TipoOrdenCompras;
use App\Models\Productos;

class OrdenComprasInsumosDetalleFactory extends Factory
{
    protected $model = OrdenComprasInsumosDetalle::class;

    public function definition(): array
    {
        $precio_unitario = $this->faker->randomFloat(2, 10, 100);
        $cantidad = $this->faker->numberBetween(1, 50);
        return [
            'orden_compra_insumo_id' => OrdenComprasInsumos::factory(),
            'tipo_orden_compra_id' => TipoOrdenCompras::factory(),
            'producto_id' => Productos::factory(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $precio_unitario * $cantidad,
            'porcentaje_grasa' => $this->faker->optional()->randomFloat(2, 2, 5),
            'porcentaje_proteina' => $this->faker->optional()->randomFloat(2, 2, 4),
            'porcentaje_humedad' => $this->faker->optional()->randomFloat(2, 80, 90),
            'anomalias' => $this->faker->boolean,
            'detalles_anomalias' => $this->faker->optional()->sentence,
        ];
    }
}