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

        foreach ($distribution as $platformName => $totalPerfiles) {
            $plataforma = Plataforma::query()->where('nombre', $platformName)->first();

            if (! $plataforma) {
                continue;
            }

            Perfil::query()->where('plataforma_id', $plataforma->id)->delete();

            for ($i = 1; $i <= $totalPerfiles; $i++) {
                $asignado = random_int(1, 100) <= 82;

                $fechaInicio = Carbon::today()->subDays(random_int(2, 26));
                $fechaCorte = (clone $fechaInicio)->addDays(30);
                $fechaCaducidad = (clone $fechaCorte)->addDays(random_int(-3, 6));

                $nombrePerfil = (string) $i;
                $correoCuenta = strtolower(str_replace(' ', '', $platformName)) . $i . '@stream.local';

                $clienteNombre = null;
                $clienteTelefono = null;
                $clienteEmail = null;
                $clienteDocumento = null;
                $clienteDireccion = null;
                $estado = 'disponible';
                $disponible = true;

                if ($asignado) {
                    $nombre = $nombres[array_rand($nombres)];
                    $apellido = $apellidos[array_rand($apellidos)];
                    $clienteNombre = "{$nombre} {$apellido}";
                    $clienteTelefono = '9' . random_int(100, 999) . '-' . random_int(1000, 9999);
                    $clienteEmail = strtolower($nombre . '.' . $apellido . random_int(1, 99)) . '@mail.com';
                    $clienteDocumento = (string) random_int(1000000000000, 9999999999999);
                    $clienteDireccion = 'Col. Centro, Tegucigalpa';
                    $disponible = false;

                    if ($fechaCaducidad->isPast()) {
                        $estado = 'vencido';
                    } elseif ($fechaCaducidad->lte(Carbon::today()->addDays(4))) {
                        $estado = 'suspendido';
                    } else {
                        $estado = 'activo';
                    }
                }

                Perfil::query()->create([
                    'plataforma_id' => $plataforma->id,
                    'nombre_perfil' => $nombrePerfil,
                    'pin' => (string) random_int(1000, 9999),
                    'cliente_nombre' => $clienteNombre,
                    'cliente_telefono' => $clienteTelefono,
                    'proveedor_nombre' => 'Proveedor ' . $platformName,
                    'correo_cuenta' => $correoCuenta,
                    'contrasena_cuenta' => 'Pass' . random_int(1000, 9999),
                    'cliente_email' => $clienteEmail,
                    'cliente_documento' => $clienteDocumento,
                    'cliente_direccion' => $clienteDireccion,
                    'estado' => $estado,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_corte' => $fechaCorte,
                    'fecha_caducidad_cuenta' => $fechaCaducidad,
                    'disponible' => $disponible,
                    'notas' => $asignado ? 'Registro demo asignado automáticamente.' : 'Perfil disponible para asignación.',
                ]);
            }
        }
    }
}
