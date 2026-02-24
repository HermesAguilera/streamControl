<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Nomina - {{ $nomina->empresa->nombre ?? 'Empresa' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 24px;
            color: #333;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-section th, .info-section td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .info-section th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .deducciones-list, .percepciones-list {
            margin: 0;
            padding: 0 0 0 5px;
            list-style-type: none;
        }
        .strong {
            font-weight: bold;
        }
        .estado-cerrada {
            color: #d00;
            font-weight: bold;
        }
        .estado-abierta {
            color: #0a0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <h1>{{ $nomina->empresa->nombre ?? 'Empresa' }}</h1>
            <p>{{ $nomina->empresa->direccion ?? '' }}</p>
            <p>Teléfono: {{ $nomina->empresa->telefono ?? '' }}</p>
            <h2>Nómina del mes de {{ $mesNombre }} {{ $nomina->año }}</h2>
        </div>

        <!-- Información general de la nómina -->
        <div class="info-section">
            <table>
                <tr>
                    <th>Fecha de generación</th>
                    <td>{{ $fechaGeneracion }}</td>
                </tr>
                <tr>
                    <th>Período</th>
                    <td>{{ $mesNombre }} {{ $nomina->año }}</td>
                </tr>
                <tr>
                    <th>Tipo de Pago</th>
                    <td>{{ ucfirst($tipoPagoNombre) }}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td class="{{ $nomina->cerrada ? 'estado-cerrada' : 'estado-abierta' }}">
                        {{ $nomina->cerrada ? 'Cerrada' : 'Abierta' }}
                    </td>
                </tr>
                @if($nomina->descripcion)
                <tr>
                    <th>Descripción</th>
                    <td>{{ $nomina->descripcion }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Detalle de empleados -->
        <h3>Detalle de Empleados</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="30%">Empleado</th>
                    <th width="15%">Salario</th>
                    <th width="22%">Deducciones</th>
                    <th width="22%">Percepciones</th>
                    <th width="11%">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalGeneral = 0; @endphp
                @foreach($empleados as $empleado)
                    @php $totalGeneral += $empleado['total']; @endphp
                    <tr>
                        <td>
                            <strong>{{ $empleado['nombre'] }}</strong>
                            @if(!empty($empleado['departamento']))
                            <br><small>Departamento: {{ $empleado['departamento'] }}</small>
                            @endif
                        </td>
                        <td class="text-right">L. {{ number_format($empleado['salario'], 2) }}</td>
                        <td>
                            <ul class="deducciones-list">
                                @php $totalDeducciones = 0; @endphp
                                @foreach($empleado['deduccionesArray'] as $deduccion)
                                    @if($deduccion['aplicada'])
                                        <li>
                                            {{ $deduccion['nombre'] }}:
                                            <span class="text-right">
                                                {{ $deduccion['valorMostrado'] ?? '' }}
                                                @if(str_ends_with(trim($deduccion['valorMostrado']), '%'))
                                                    (L. {{ number_format($deduccion['valorCalculado'], 2) }})
                                                @endif
                                            </span>
                                        </li>
                                        @php $totalDeducciones += $deduccion['valorCalculado'] ?? 0; @endphp
                                    @endif
                                @endforeach
                                @if(count($empleado['deduccionesArray']) > 0)
                                <li class="strong">Total: L. {{ number_format($totalDeducciones, 2) }}</li>
                                @else
                                <li>Ninguna</li>
                                @endif
                            </ul>
                        </td>
                        <td>
                            <ul class="percepciones-list">
                                @php $totalPercepciones = 0; @endphp
                                @foreach($empleado['percepcionesArray'] as $percepcion)
                                    @if($percepcion['aplicada'])
                                        <li>
                                            {{ $percepcion['nombre'] }}:
                                            <span class="text-right">
                                                {{ $percepcion['valorMostrado'] ?? '' }}
                                                @if(str_ends_with(trim($percepcion['valorMostrado']), '%'))
                                                    (L. {{ number_format($percepcion['valorCalculado'], 2) }})
                                                @endif
                                            </span>
                                        </li>
                                        @php $totalPercepciones += $percepcion['valorCalculado'] ?? 0; @endphp
                                    @endif
                                @endforeach
                                @if(count($empleado['percepcionesArray']) > 0)
                                <li class="strong">Total: L. {{ number_format($empleado['percepciones'], 2) }}</li>
                                @else
                                <li>Ninguna</li>
                                @endif
                            </ul>
                        </td>
                        <td class="text-right"><strong>L. {{ number_format($empleado['total'], 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right strong">TOTAL NÓMINA:</td>
                    <td class="text-right strong">L. {{ number_format($totalGeneral, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Firmas -->
        <div style="margin-top: 50px;">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 33%; text-align: center;">
                        __________________________<br>
                        Elaborado por
                    </td>
                    <td style="width: 33%; text-align: center;">
                        __________________________<br>
                        Revisado por
                    </td>
                    <td style="width: 33%; text-align: center;">
                        __________________________<br>
                        Autorizado por
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Este documento es una representación digital de la nómina de empleados</p>
            <p>Generado el {{ $fechaGeneracion }} - {{ $nomina->empresa->nombre ?? 'Empresa' }}</p>
        </div>
    </div>
</body>
</html>
