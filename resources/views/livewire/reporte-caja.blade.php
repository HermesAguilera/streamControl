<div>
    <h2>Reporte de Arqueo de Caja</h2>
    <div>
        <strong>Usuario:</strong> {{ $apertura->usuario->name ?? '' }}<br>
        <strong>Monto Inicial:</strong> {{ number_format($apertura->monto_inicial, 2) }}<br>
        <strong>Fecha Apertura:</strong> {{ $apertura->fecha_apertura }}<br>
        <strong>Fecha Cierre:</strong> {{ $apertura->fecha_cierre }}<br>
        <strong>Estado:</strong> {{ ucfirst($apertura->estado) }}<br>
    </div>
    <hr>
    <h3>Totales por Método de Pago</h3>
    <ul>
        <li>Efectivo: {{ number_format($totales['efectivo'], 2) }}</li>
        <li>Tarjeta: {{ number_format($totales['tarjeta'], 2) }}</li>
        <li>Transferencia: {{ number_format($totales['transferencia'], 2) }}</li>
        <li>Otros: {{ number_format($totales['otros'], 2) }}</li>
    </ul>
    <hr>
    <h3>Facturas</h3>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Método Pago</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturas as $factura)
                <tr>
                    <td>{{ $factura->numero_factura }}</td>
                    <td>{{ $factura->cliente->nombre ?? '' }}</td>
                    <td>{{ ucfirst($factura->metodo_pago) }}</td>
                    <td>{{ number_format($factura->total, 2) }}</td>
                    <td>{{ $factura->fecha_factura }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
