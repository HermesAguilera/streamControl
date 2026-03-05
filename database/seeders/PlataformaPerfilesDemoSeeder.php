<?php

namespace Database\Seeders;

use App\Models\Perfil;
use App\Models\Plataforma;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PlataformaPerfilesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $distribution = [
            'Netflix' => 34,
            'Amazon Prime Video' => 21,
            'HBO Max' => 11,
        ];

        $nombres = [
            'Carlos', 'Ana', 'Luis', 'María', 'José', 'Daniela', 'Jorge', 'Karla', 'Miguel', 'Andrea',
            'Sofía', 'Ricardo', 'Marta', 'Pablo', 'Fernanda', 'Héctor', 'Valeria', 'Raúl', 'Natalia', 'Elías',
        ];

        $apellidos = [
            'López', 'Martínez', 'Hernández', 'García', 'Ramírez', 'Flores', 'Cruz', 'Santos', 'Mendoza', 'Pineda',
            'Castro', 'Mejía', 'Figueroa', 'Bonilla', 'Chávez', 'Aguilar', 'Suazo', 'Vargas', 'Reyes', 'Rivas',
        ];

        // Se eliminó la creación de perfiles y clientes demo para evitar datos de ejemplo en producción.
    }
}
