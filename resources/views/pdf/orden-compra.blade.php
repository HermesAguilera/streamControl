<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra {{ $orden->id }}</title>
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
        .header img {
            max-width: 150px;
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
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <h1>{{ $orden->empresa->nombre }}</h1>
            <p>{{ $orden->empresa->direccion }}</p>
            <p>Teléfono: {{ $orden->empresa->telefono }}</p>
            <h2>Orden de Compra N° {{ $orden->id }}</h2>
        </div>

        <!-- Información de la orden -->
        <div class="info-section">
            <table>
                <tr>
                    <th>Fecha Realizada</th>
                    <td>{{ \Carbon\Carbon::parse($orden->fecha_realizada)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Tipo de Orden</th>
                    <td>{{ $orden->tipoOrdenCompra->nombre }}</td>
                </tr>
                <tr>
                    <th>Proveedor</th>
                    <td>{{ $orden->proveedor->nombre_proveedor }}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td>{{ $orden->estado === 'Recibida' ? 'Orden en Inventario' : 'Orden Abierta' }}</td>
                </tr>
                @if ($orden->descripcion)
                <tr>
                    <th>Descripción</th>
                    <td>{{ $orden->descripcion }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Detalles de productos -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Subcategoría</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario (HNL)</th>
                    <th>Subtotal (HNL)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orden->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td>{{ optional($detalle->producto->categoria)->nombre ?? 'Sin categoría' }}</td>
                    <td>{{ optional($detalle->producto->subcategoria)->nombre ?? 'Sin subcategoría' }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>{{ number_format($detalle->precio, 2) }}</td>
                    <td>{{ number_format($detalle->cantidad * $detalle->precio, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total -->
        <div class="total">
            <p>Total: HNL {{ number_format($orden->detalles->sum(fn ($detalle) => $detalle->cantidad * $detalle->precio), 2) }}</p>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>Generado el {{ $fechaGeneracion }}</p>
            <p>Documento emitido por {{ $orden->empresa->nombre }}</p>
        </div>
    </div>
</body>
</html>