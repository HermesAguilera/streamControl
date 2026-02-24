<!DOCTYPE html>
<html>
<head>
    <title>Orden de Compra Insumos #{{ $orden->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; }
        h1, h2, h3 { text-align: center; }
    </style>
</head>
<body>
    <h1>Orden de Compra Insumos #{{ $orden->id }}</h1>
    <p><strong>Empresa:</strong> {{ $orden->empresa->nombre }}</p>
    <p><strong>Proveedor:</strong> {{ $orden->proveedor->nombre_proveedor }}</p>
    <p><strong>Fecha:</strong> {{ $orden->fecha_realizada->format('d/m/Y') }}</p>
    <p><strong>Generado:</strong> {{ $fechaGeneracion }}</p>

    <h3>Análisis de Calidad</h3>
    <p><strong>Grasa:</strong> {{ $orden->porcentaje_grasa ?? 'N/A' }}%</p>
    <p><strong>Proteína:</strong> {{ $orden->porcentaje_proteina ?? 'N/A' }}%</p>
    <p><strong>Humedad:</strong> {{ $orden->porcentaje_humedad ?? 'N/A' }}%</p>
    <p><strong>Anomalías:</strong> {{ $orden->anomalias ? 'Sí' : 'No' }}</p>
    @if($orden->anomalias)
        <p><strong>Detalles de Anomalías:</strong> {{ $orden->detalles_anomalias }}</p>
    @endif

    <h3>Detalles</h3>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orden->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>{{ number_format($detalle->precio_unitario, 2) }} HNL</td>
                    <td>{{ number_format($detalle->subtotal, 2) }} HNL</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>