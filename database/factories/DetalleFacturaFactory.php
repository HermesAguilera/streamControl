<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\detalle_factura>
 */
class DetalleFacturaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cantidad = $this->faker->randomFloat(2, 1, 10);
        $precio = $this->faker->randomFloat(2, 10, 100);
        $subTotal = $cantidad * $precio;

        return [
            'factura_id' => \App\Models\Factura::factory(),
            'producto_id' => \App\Models\InventarioProductos::factory(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'sub_total' => $subTotal,
            'total_factura' => $subTotal, // puede cambiar si hay impuestos
        ];
    }
}
