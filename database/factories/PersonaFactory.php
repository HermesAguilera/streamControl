<?php

namespace Database\Factories;

use App\Models\Persona;
use App\Models\Municipio;
use App\Models\Paises;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonaFactory extends Factory
{
    protected $model = Persona::class;

    public function definition(): array
    {
        return [
            'primer_nombre' => $this->faker->firstName(),
            'segundo_nombre' => $this->faker->optional()->firstName(),
            'primer_apellido' => $this->faker->lastName(),
            'segundo_apellido' => $this->faker->optional()->lastName(),
            'dni' => $this->faker->unique()->randomNumber(8),
            'direccion' => $this->faker->address(),
            'municipio_id' => Municipio::inRandomOrder()->first()->id ?? 1,
            'departamento_id' => \App\Models\Departamento::inRandomOrder()->first()->id ?? 1,
            'telefono' => $this->faker->phoneNumber(),
            'sexo' => $this->faker->randomElement(['MASCULINO', 'FEMENINO']), // Ajuste para coincidir con ENUM
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'pais_id' => Paises::inRandomOrder()->first()->id ?? 1,
            'fotografia' => $this->faker->optional()->imageUrl(200, 200),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}