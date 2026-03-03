@php
    /** @var \App\Models\Cuenta $cuenta */
    $cuenta->loadMissing(['plataforma']);

    $campos = [
        '# de cuenta' => $cuenta->id,
        'Proveedor' => $cuenta->proveedor,
        'Plataforma' => $cuenta->plataforma?->nombre,
        'Correo de la cuenta' => $cuenta->correo,
        'Contraseña de la cuenta' => $cuenta->contrasena,
        'Perfiles asociados' => $cuenta->perfiles()->count(),
        'Fecha de inicio' => optional($cuenta->fecha_inicio)->format('Y-m-d'),
        'Fecha de corte' => optional($cuenta->fecha_corte)->format('Y-m-d'),
    ];

    $valorTexto = function ($value): string {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'Sí' : 'No';
        }

        return (string) $value;
    };
@endphp

<div class="rounded-xl border border-gray-200 p-3 sm:p-4">
    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
        @foreach ($campos as $etiqueta => $valor)
            <div class="rounded-lg border border-gray-100 px-3 py-2">
                <p class="text-xs font-medium text-gray-500">{{ $etiqueta }}</p>
                <p class="mt-1 text-sm text-gray-900">{{ $valorTexto($valor) }}</p>
            </div>
        @endforeach
    </div>
</div>
