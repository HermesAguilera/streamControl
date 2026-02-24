<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 16px; font-weight: bold;">{{ $nomina->empresa->nombre ?? 'Empresa' }}</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;">{{ $nomina->empresa->direccion ?? '' }}</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;">Teléfono: {{ $nomina->empresa->telefono ?? '' }}</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 14px; font-weight: bold;">Nómina del mes de {{ $mesNombre }} {{ $nomina->año }}</th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th style="font-weight: bold;">Fecha de generación</th>
            <td colspan="4">{{ $fechaGeneracion }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Período</th>
            <td colspan="4">{{ $mesNombre }} {{ $nomina->año }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Tipo de Pago</th>
            <td colspan="4">{{ ucfirst($tipoPagoNombre) }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Estado</th>
            <td colspan="4">{{ $nomina->cerrada ? 'Cerrada' : 'Abierta' }}</td>
        </tr>
        @if($nomina->descripcion)
        <tr>
            <th style="font-weight: bold;">Descripción</th>
            <td colspan="4">{{ $nomina->descripcion }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <th colspan="5" style="text-align: left; font-weight: bold;">Detalle de Empleados</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f2f2f2;">Empleado</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Salario</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Deducciones</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Percepciones</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Total</th>
        </tr>
        @foreach($empleados as $empleado)
            <tr>
                <td>{{ $empleado['nombre'] }}{{ !empty($empleado['departamento']) ? ' (Depto: '.$empleado['departamento'].')' : '' }}</td>
                <td style="text-align: right;">L. {{ number_format($empleado['salario'], 2) }}</td>
                <td>
                    @php $totalDeducciones = 0; @endphp
                    @foreach($empleado['deduccionesArray'] as $deduccion)
                        @if($deduccion['aplicada'])
                            {{ $deduccion['nombre'] }}: {{ $deduccion['valorMostrado'] ?? '' }}
                            @if(str_ends_with(trim($deduccion['valorMostrado']), '%'))
                                (L. {{ number_format($deduccion['valorCalculado'], 2) }})
                            @endif
                            @php $totalDeducciones += $deduccion['valorCalculado'] ?? 0; @endphp
                            @if(!$loop->last){{ ', ' }}@endif
                        @endif
                    @endforeach
                    @if(count($empleado['deduccionesArray']) > 0)
                        Total: L. {{ number_format($totalDeducciones, 2) }}
                    @else
                        Ninguna
                    @endif
                </td>
                <td>
                    @php $totalPercepciones = 0; @endphp
                    @foreach($empleado['percepcionesArray'] as $percepcion)
                        @if($percepcion['aplicada'])
                            {{ $percepcion['nombre'] }}: {{ $percepcion['valorMostrado'] ?? '' }}
                            @if(str_ends_with(trim($percepcion['valorMostrado']), '%'))
                                (L. {{ number_format($percepcion['valorCalculado'], 2) }})
                            @endif
                            @php $totalPercepciones += $percepcion['valorCalculado'] ?? 0; @endphp
                            @if(!$loop->last){{ ', ' }}@endif
                        @endif
                    @endforeach
                    @if(count($empleado['percepcionesArray']) > 0)
                        Total: L. {{ number_format($empleado['percepciones'], 2) }}
                    @else
                        Ninguna
                    @endif
                </td>
                <td style="text-align: right; font-weight: bold;">L. {{ number_format($empleado['total'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL NÓMINA:</td>
            <td style="text-align: right; font-weight: bold;">L. {{ number_format($totalNomina, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: center;">Este documento es una representación digital de la nómina de empleados</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: center;">Generado el {{ $fechaGeneracion }} - {{ $nomina->empresa->nombre ?? 'Empresa' }}</td>
        </tr>
    </tbody>
</table>
