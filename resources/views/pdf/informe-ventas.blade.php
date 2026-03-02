<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de ventas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 24px;
            font-size: 12px;
        }

        .header {
            background: linear-gradient(90deg, #111827 0%, #1f2937 100%);
            color: #fff;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .header .meta {
            margin-top: 8px;
            font-size: 11px;
            opacity: .95;
        }

        .cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 16px;
        }

        .card {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            padding: 10px;
            text-align: center;
        }

        .card .label {
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }

        .card .value {
            font-size: 20px;
            font-weight: 700;
        }

        .table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #f3f4f6;
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .4px;
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .text-center { text-align: center; }
        .text-success { color: #047857; }
        .text-danger { color: #b91c1c; }

        .footer-note {
            margin-top: 14px;
            color: #6b7280;
            font-size: 10px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    @php
        $resumen = $report['resumen'] ?? [];
        $rows = $report['plataformas'] ?? [];
        $moneda = $report['moneda'] ?? 'L';
    @endphp

    <div class="header">
        <h1>Informe de ventas y pérdidas</h1>
        <div class="meta">
            Período: {{ $report['periodo_label'] ?? '-' }} | Rango: {{ $report['start'] ?? '-' }} a {{ $report['end'] ?? '-' }}<br>
            Ticket base (fallback): {{ $moneda }} {{ number_format((float) ($report['ticket_promedio'] ?? 0), 2) }}<br>
            Generado: {{ $report['generated_at'] ?? '-' }} | Usuario: {{ $report['generated_by'] ?? 'Sistema' }}
        </div>
    </div>

    <table class="cards">
        <tr>
            <td class="card">
                <div class="label">Vendidos</div>
                <div class="value">{{ $resumen['vendidos'] ?? 0 }}</div>
            </td>
            <td class="card">
                <div class="label">Dejados de vender</div>
                <div class="value text-danger">{{ $resumen['dejados'] ?? 0 }}</div>
            </td>
            <td class="card">
                <div class="label">Balance neto</div>
                <div class="value {{ ($resumen['neto'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">{{ $resumen['neto'] ?? 0 }}</div>
            </td>
            <td class="card">
                <div class="label">Clientes activos</div>
                <div class="value">{{ $resumen['activos'] ?? 0 }}</div>
            </td>
            <td class="card">
                <div class="label">Retención</div>
                <div class="value">{{ $resumen['retencion'] ?? 0 }}%</div>
            </td>
        </tr>
    </table>

    <table class="cards">
        <tr>
            <td class="card">
                <div class="label">Ingreso vendido</div>
                <div class="value text-success">{{ $moneda }} {{ number_format((float) ($resumen['ingresos_vendidos'] ?? 0), 2) }}</div>
            </td>
            <td class="card">
                <div class="label">Ingreso perdido</div>
                <div class="value text-danger">{{ $moneda }} {{ number_format((float) ($resumen['ingresos_perdidos'] ?? 0), 2) }}</div>
            </td>
            <td class="card">
                <div class="label">Ingreso neto</div>
                <div class="value {{ ((float) ($resumen['ingresos_netos'] ?? 0)) >= 0 ? 'text-success' : 'text-danger' }}">{{ $moneda }} {{ number_format((float) ($resumen['ingresos_netos'] ?? 0), 2) }}</div>
            </td>
        </tr>
    </table>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Plataforma</th>
                    <th class="text-center">Vendidos</th>
                    <th class="text-center">Dejados</th>
                    <th class="text-center">Neto</th>
                    <th class="text-center">Ticket prom.</th>
                    <th class="text-center">Ingreso vendido</th>
                    <th class="text-center">Ingreso perdido</th>
                    <th class="text-center">Ingreso neto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>{{ $row['plataforma'] }}</td>
                        <td class="text-center">{{ $row['vendidos'] }}</td>
                        <td class="text-center">{{ $row['dejados'] }}</td>
                        <td class="text-center {{ $row['neto'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $row['neto'] }}</td>
                        <td class="text-center">{{ $moneda }} {{ number_format((float) ($row['ticket_promedio'] ?? 0), 2) }}</td>
                        <td class="text-center text-success">{{ $moneda }} {{ number_format((float) ($row['ingresos_vendidos'] ?? 0), 2) }}</td>
                        <td class="text-center text-danger">{{ $moneda }} {{ number_format((float) ($row['ingresos_perdidos'] ?? 0), 2) }}</td>
                        <td class="text-center {{ ((float) ($row['ingresos_netos'] ?? 0)) >= 0 ? 'text-success' : 'text-danger' }}">{{ $moneda }} {{ number_format((float) ($row['ingresos_netos'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay datos disponibles para el período seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="footer-note">
        Criterio del informe: "Vendidos" considera clientes creados en el período. "Dejados de vender" considera clientes con fecha de caducidad dentro del período y vencidos a la fecha de generación. Los montos se calculan con ticket promedio configurable por cliente para análisis comercial.
    </p>
</body>
</html>
