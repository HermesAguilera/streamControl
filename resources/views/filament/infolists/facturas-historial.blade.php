<div>
    @if($getRecord()->facturas && $getRecord()->facturas->count() > 0)
        <div class="space-y-3">
            @foreach($getRecord()->facturas as $factura)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    #{{ str_pad($factura->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $factura->fecha_factura ? \Carbon\Carbon::parse($factura->fecha_factura)->format('d/m/Y') : 'Sin fecha' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-green-600 dark:text-green-400">
                                    L. {{ number_format($factura->total ?? 0, 2) }}
                                </span>
                            </div>
                            <div>
                                @php
                                    $estadoColor = match($factura->estado ?? 'Sin estado') {
                                        'pagado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'pendiente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'vencido' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoColor }}">
                                    {{ ucfirst($factura->estado ?? 'Sin estado') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            Este cliente (Consumidor Mayorista) aún no tiene facturas registradas
        </div>
    @endif
</div>
