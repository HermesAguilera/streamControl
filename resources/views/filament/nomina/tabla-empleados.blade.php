@if ($empleados->isEmpty())
    <p>No hay registros en el historial de pagos.</p>
@else
    <table style="width:100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th style="border: 1px solid #ccc; padding: 8px;">Número</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Nombre</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Sueldo</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Deducciones</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Percepciones</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Sueldo Neto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empleados as $detalle)
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        {{ $detalle->empleado?->numero_empleado ?? '-' }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        {{ $detalle->empleado?->getNombreCompletoAttribute() ?? '-' }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        L. {{ number_format($detalle->sueldo_bruto ?? 0, 2) }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        L. {{ number_format($detalle->deducciones ?? 0, 2) }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        L. {{ number_format($detalle->percepciones ?? 0, 2) }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        L. {{ number_format($detalle->sueldo_neto ?? 0, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

