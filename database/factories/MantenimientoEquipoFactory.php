<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\mantenimiento_equipo>
 */
class MantenimientoEquipoFactory extends Factory
{
    protected $model = MantenimientoEquipo::class;

    public function definition()
    {
        return [
            'tipo_tarea_id' => \App\Models\TipoTareaLimpiezaMantenimiento::factory(),
            'productos_id' => \App\Models\Producto::factory(),
            'descripcion_tarea' => $this->faker->sentence,
            'fecha_hora_programada' => $this->faker->dateTimeBetween('now', '+1 month'),
            'fecha_hora_realizada' => null,
            'estado' => $this->faker->randomElement(['PENDIENTE', 'EN_PROCESO', 'REALIZADO', 'NO_REALIZADO']),
            'empleado_id' => \App\Models\Empleado::factory(),
            'observaciones' => $this->faker->optional()->paragraph,
        ];
    }
}
