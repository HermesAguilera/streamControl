@php
    /** @var \App\Models\Perfil $perfil */
    $perfil->loadMissing(['plataforma', 'cuenta']);

    $cantidadPerfiles = filled($perfil->cuenta_id)
        ? \App\Models\Perfil::query()
            ->where('plataforma_id', $perfil->plataforma_id)
            ->where('cliente_nombre', $perfil->cliente_nombre)
            ->where('cuenta_id', $perfil->cuenta_id)
            ->count()
        : \App\Models\Perfil::query()
            ->where('plataforma_id', $perfil->plataforma_id)
            ->where('cliente_nombre', $perfil->cliente_nombre)
            ->whereRaw('LOWER(TRIM(correo_cuenta)) = ?', [strtolower(trim((string) $perfil->correo_cuenta))])
            ->count();

    $valorTexto = function ($value): string {
        if ($value === null || $value === '') {
            return '—';
        }

        return (string) $value;
    };

    $campos = [
        '# de cliente' => $perfil->id,
        'Proveedor' => $perfil->proveedor_nombre,
        'Plataforma' => $perfil->plataforma?->nombre,
        'Fecha de caducidad (cuenta)' => optional($perfil->fecha_caducidad_cuenta)->format('Y-m-d'),
        'Correo de la cuenta' => $perfil->correo_cuenta,
        'Contraseña de la cuenta' => $perfil->contrasena_cuenta,
        'Nombre del cliente' => $perfil->cliente_nombre,
        'Número de teléfono del cliente' => $perfil->cliente_telefono,
        'PIN de perfil' => $perfil->pin,
        'Cantidad de perfiles' => $cantidadPerfiles,
        'Fecha de inicio (registro cliente)' => optional($perfil->fecha_inicio)->format('Y-m-d'),
        'Fecha de corte' => optional($perfil->fecha_corte)->format('Y-m-d'),
    ];
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
