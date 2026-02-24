<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Cierre de Caja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px; /* Reducido el margen */
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .report-title {
            font-size: 16px;
            color: #666;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            width: 25%;
            padding: 2px 5px;
            vertical-align: top;
        }
        .info-table .info-table-label {
            font-weight: bold;
            text-align: left;
        }
        .info-table .info-table-value {
            text-align: left;
        }
        
        .signature-table {
            width: 100%;
            margin-top: 50px;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            padding: 0 20px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 50px;
        }
        /* --- FIN DE ESTILOS MODIFICADOS --- */

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding: 5px 0;
            border-bottom: 1px solid #ccc;
        }
        .total-row {
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
        }
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .comparison-table th,
        .comparison-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        .comparison-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .notes-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007cba;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="report-title">Reporte de Cierre de Caja</div>
    </div>

    {{-- ========================================================== --}}
    {{--          SECCIÓN DE INFORMACIÓN GENERAL MODIFICADA         --}}
    {{-- ========================================================== --}}
    <table class="info-table">
        <tr>
            <td class="info-table-label">Usuario:</td>
            <td class="info-table-value">{{ $apertura->user->name ?? 'N/A' }}</td>
            <td class="info-table-label">Fecha de Apertura:</td>
            <td class="info-table-value">{{ $apertura->created_at->format('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td class="info-table-label">Empresa:</td>
            <td class="info-table-value">{{ $apertura->user->empresa->nombre ?? 'N/A' }}</td>
            <td class="info-table-label">Fecha de Cierre:</td>
            <td class="info-table-value">{{ now()->format('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td class="info-table-label">Caja Nº:</td>
            <td class="info-table-value">{{ $apertura->id }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    
    {{-- Resumen de Ventas --}}
    <div class="section-title">Resumen de Ventas del Sistema</div>
    <div class="info-section">
        <div style="display: flex; justify-content: space-between; padding: 5px 0;">
            <span style="font-weight: bold;">Monto Inicial:</span>
            <span>L {{ number_format($apertura->monto_inicial, 2) }}</span>
        </div>
        
        @foreach ($reporteSistema as $metodo => $total)
            <div style="display: flex; justify-content: space-between; padding: 5px 0;">
                <span style="font-weight: bold;">Ventas {{ $metodo }}:</span>
                <span>L {{ number_format($total, 2) }}</span>
            </div>
        @endforeach
        
        <div class="total-row" style="display: flex; justify-content: space-between;">
            <span>Total Esperado en Efectivo:</span>
            <span>L {{ number_format($totalEnCajaEsperado, 2) }}</span>
        </div>
    </div>

    {{-- Comparación Conteo vs Sistema --}}
    <div class="section-title">Comparación: Sistema vs Conteo Manual</div>
    <table class="comparison-table">
        <thead>
            <tr>
                <th style="text-align: left;">Método de Pago</th>
                <th>Sistema</th>
                <th>Contado</th>
                <th>Diferencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($diferencias as $metodo => $detalle)
                <tr>
                    <td style="text-align: left;">{{ $metodo }}</td>
                    <td>L {{ number_format($detalle['sistema'], 2) }}</td>
                    <td>L {{ number_format($detalle['contado'], 2) }}</td>
                    <td style="color: {{ $detalle['diferencia'] >= 0 ? '#2d5a2d' : '#5a2d2d' }};">
                        @if($detalle['diferencia'] > 0)
                            +L {{ number_format($detalle['diferencia'], 2) }}
                        @elseif($detalle['diferencia'] < 0)
                            L {{ number_format($detalle['diferencia'], 2) }}
                        @else
                            L 0.00
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Resumen de Diferencias --}}
    @php
        $totalDiferencias = collect($diferencias)->sum('diferencia');
        $estadoCierre = abs($totalDiferencias) <= 1 ? 'Correcto' : ($totalDiferencias > 0 ? 'Sobrante' : 'Faltante');
    @endphp

    <div class="section-title">Estado Final del Cierre</div>
    <div style="display: flex; justify-content: space-between; padding: 5px 0;">
        <span style="font-weight: bold;">Diferencia Total:</span>
        <span style="color: {{ $totalDiferencias >= 0 ? '#2d5a2d' : '#5a2d2d' }};">
            @if($totalDiferencias > 0)
                +L {{ number_format($totalDiferencias, 2) }} ({{ $estadoCierre }})
            @elseif($totalDiferencias < 0)
                L {{ number_format($totalDiferencias, 2) }} ({{ $estadoCierre }})
            @else
                L 0.00 ({{ $estadoCierre }})
            @endif
        </span>
    </div>
    <div style="display: flex; justify-content: space-between; padding: 5px 0;">
        <span style="font-weight: bold;">Estado del Cierre:</span>
        <span>
            @if(abs($totalDiferencias) <= 1)
                 Aprobado
            @else
                 Requiere Revisión
            @endif
        </span>
    </div>

    {{-- Notas del Cierre --}}
    @if(!empty($notasCierre))
        <div class="section-title">Notas del Cierre</div>
        <div class="notes-section">
            <p>{{ $notasCierre }}</p>
        </div>
    @endif

    {{-- ========================================================== --}}
    {{--             SECCIÓN DE FIRMAS MODIFICADA                   --}}
    {{-- ========================================================== --}}
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                <div>Cajero</div>
                <div>{{ $apertura->user->name ?? '' }}</div>
            </td>
            <td>
                <div class="signature-line"></div>
                <div>Supervisor</div>
                <div>Firma y Sello</div>
            </td>
        </tr>
    </table>
</body>
</html>