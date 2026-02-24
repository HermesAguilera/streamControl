<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Paises;
use App\Models\Persona;
use App\Models\Plataforma;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $pais = Paises::firstOrCreate(
            ['nombre_pais' => 'Honduras']
        );

        $departamento = Departamento::firstOrCreate(
            ['nombre_departamento' => 'Francisco Morazán'],
            ['pais_id' => $pais->id]
        );

        $municipio = Municipio::firstOrCreate(
            ['nombre_municipio' => 'Distrito Central'],
            [
                'pais_id' => $pais->id,
                'departamento_id' => $departamento->id,
            ]
        );

        $empresa = Empresa::firstOrCreate(
            ['rtn' => '0801199900012'],
            [
                'nombre' => 'Streaming Demo',
                'pais_id' => $pais->id,
                'departamento_id' => $departamento->id,
                'municipio_id' => $municipio->id,
                'direccion' => 'Tegucigalpa',
                'telefono' => '0000-0000',
            ]
        );

        $this->call([
            RolesSeeder::class,
            RolesPermissionsSeeder::class,
        ]);

        $personaAdmin = Persona::firstOrCreate(
            ['dni' => '9999999999999'],
            [
                'primer_nombre' => 'Admin',
                'primer_apellido' => 'Sistema',
                'tipo_persona' => 'natural',
                'direccion' => 'Tegucigalpa',
                'municipio_id' => $municipio->id,
                'departamento_id' => $departamento->id,
                'pais_id' => $pais->id,
                'empresa_id' => $empresa->id,
                'telefono' => '0000-0000',
                'sexo' => 'OTRO',
                'fecha_nacimiento' => '1990-01-01',
            ]
        );

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@streaming.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa->id,
                'persona_id' => $personaAdmin->id,
            ]
        );

        $adminUser->syncRoles(['administrador']);

        Plataforma::firstOrCreate(['nombre' => 'Netflix'], ['activa' => true]);
        Plataforma::firstOrCreate(['nombre' => 'Amazon Prime Video'], ['activa' => true]);
        Plataforma::firstOrCreate(['nombre' => 'HBO Max'], ['activa' => true]);

        $this->call([
            PlataformaPerfilesDemoSeeder::class,
        ]);
    }
}
