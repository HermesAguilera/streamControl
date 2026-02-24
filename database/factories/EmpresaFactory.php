<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Departamento;
use App\Models\Paises;

class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;
    

    public function definition()
    {
            $departamento = Departamento::inRandomOrder()->first();

        return [
            'nombre' => $this->faker->company,
            'municipio_id' => 1, // o un municipio válido
            'pais_id' => 1, // un país válido
            'departamento_id' => $departamento ? $departamento->id : 1, // asignar un departamento válido
            'direccion' => $this->faker->address,
            'telefono' => $this->faker->phoneNumber,
            'rtn' => $this->faker->unique()->numerify('##########'),
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
