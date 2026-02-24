<x-filament::page>
    <x-filament::card>
        @if($orden)
            <h2 class="text-xl font-bold">Recibir Orden de Compra Insumos #{{ $orden->id }}</h2>
            <p><strong>Proveedor:</strong> {{ $orden->proveedor->nombre_proveedor }}</p>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($orden->fecha_realizada)->format('d/m/Y') }}</p>
            <p><strong>Estado:</strong> {{ $orden->estado }}</p>

            <h3 class="mt-4 text-lg font-semibold">Detalles</h3>
            <table class="w-full mt-2 border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Producto</th>
                        <th class="p-2 border">Tipo de Orden</th>
                        <th class="p-2 border">Cantidad</th>
                        <th class="p-2 border">Precio Unitario</th>
                        <th class="p-2 border">Subtotal</th>
                        <th class="p-2 border">Grasa (%)</th>
                        <th class="p-2 border">Proteína (%)</th>
                        <th class="p-2 border">Humedad (%)</th>
                        <th class="p-2 border">Anomalías</th>
                        <th class="p-2 border">Detalles de Anomalías</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orden->detalles as $detalle)
                        <tr>
                            <td class="p-2 border">{{ $detalle->producto->nombre }}</td>
                            <td class="p-2 border">{{ $detalle->tipoOrdenCompra->nombre }}</td>
                            <td class="p-2 border">{{ $detalle->cantidad }}</td>
                            <td class="p-2 border">{{ number_format($detalle->precio_unitario, 2) }} HNL</td>
                            <td class="p-2 border">{{ number_format($detalle->subtotal, 2) }} HNL</td>
                            <td class="p-2 border">{{ $detalle->porcentaje_grasa ? number_format($detalle->porcentaje_grasa, 2) . '%' : 'N/A' }}</td>
                            <td class="p-2 border">{{ $detalle->porcentaje_proteina ? number_format($detalle->porcentaje_proteina, 2) . '%' : 'N/A' }}</td>
                            <td class="p-2 border">{{ $detalle->porcentaje_humedad ? number_format($detalle->porcentaje_humedad, 2) . '%' : 'N/A' }}</td>
                            <td class="p-2 border">{{ $detalle->anomalias ? 'Sí' : 'No' }}</td>
                            <td class="p-2 border">{{ $detalle->detalles_anomalias ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($orden->estado === 'Pendiente')
                <x-filament::button wire:click="recibir" color="success" class="mt-4">
                    Recibir en Inventario
                </x-filament::button>
            @else
                <x-filament::badge color="success" class="mt-4">
                    Orden ya recibida en inventario
                </x-filament::badge>
            @endif
        @else
            <p class="text-red-500">No se encontró la orden.</p>
        @endif
    </x-filament::card>
</x-filament::page>