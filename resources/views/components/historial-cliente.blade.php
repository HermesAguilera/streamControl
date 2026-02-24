<div>
    <h4>Historial de Compras</h4>
    @if($cliente->compras && $cliente->compras->count())
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:4px;">Fecha</th>
                    <th style="text-align:left;padding:4px;">Producto</th>
                    <th style="text-align:left;padding:4px;">Cantidad</th>
                    <th style="text-align:left;padding:4px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cliente->compras as $compra)
                    <tr>
                        <td style="padding:4px;">{{ $compra->fecha }}</td>
                        <td style="padding:4px;">{{ $compra->producto }}</td>
                        <td style="padding:4px;">{{ $compra->cantidad }}</td>
                        <td style="padding:4px;">${{ number_format($compra->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay compras registradas para este cliente.</p>
    @endif
</div>
