@props(['cliente'])

<div class="filament-tables-container rounded-xl border border-gray-300 bg-white shadow-sm overflow-hidden dark:bg-gray-800 dark:border-gray-700">
    <table class="w-full text-start divide-y table-auto dark:divide-gray-700">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
                <th class="p-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <span>Fecha</span>
                </th>
                <th class="p-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <span>No. Factura</span>
                </th>
                <th class="p-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <span>Total</span>
                </th>
                <th class="p-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <span>Estado</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y dark:divide-gray-700">
            @php
                // Solo mostraremos datos de muestra para simplificar
                $facturasMuestra = [];
                $estados = ['Pagada', 'Pendiente', 'Cancelada'];
                $fechaBase = now()->subMonths(6);
                
                // Asegurar diferentes fechas para cada cliente usando su ID como semilla
                srand(($cliente->id ?? 1) * 1000);
                
                for($i = 1; $i <= 5; $i++) {
                    $fecha = $fechaBase->copy()->addDays(rand(1, 180));
                    $facturasMuestra[] = (object)[
                        'fecha' => $fecha->format('Y-m-d'),
                        'numero' => 'F-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                        'total' => rand(1000, 50000) / 100,
                        'estado' => $estados[array_rand($estados)]
                    ];
                }
                
                // Ordenar por fecha (más reciente primero)
                usort($facturasMuestra, function($a, $b) {
                    return strtotime($b->fecha) - strtotime($a->fecha);
                });
                
                $facturas = collect($facturasMuestra);
                
                // Restaurar la semilla aleatoria
                srand();
            @endphp
            
            @forelse($facturas as $factura)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/70">
                    <td class="p-4 align-middle text-sm">
                        <div class="flex items-center">
                            <span>{{ date('d/m/Y', strtotime($factura->fecha)) }}</span>
                        </div>
                    </td>
                    <td class="p-4 align-middle text-sm">
                        <div class="flex items-center font-medium">
                            {{ $factura->numero }}
                        </div>
                    </td>
                    <td class="p-4 align-middle text-sm">
                        <div class="flex items-center">
                            <span class="font-medium">L. {{ number_format($factura->total, 2) }}</span>
                        </div>
                    </td>
                    <td class="p-4 align-middle text-sm">
                        @php
                            $estado = $factura->estado;
                            $colorClase = match($estado) {
                                'Pagada' => 'text-green-800 bg-green-100 dark:bg-green-800/20 dark:text-green-400',
                                'Pendiente' => 'text-yellow-800 bg-yellow-100 dark:bg-yellow-800/20 dark:text-yellow-400', 
                                'Cancelada' => 'text-red-800 bg-red-100 dark:bg-red-800/20 dark:text-red-400',
                                default => 'text-gray-800 bg-gray-100 dark:bg-gray-800/20 dark:text-gray-400'
                            };
                        @endphp
                        <div class="px-2 py-1 inline-flex items-center gap-1 justify-center rounded-full text-xs font-medium {{ $colorClase }}">
                            {{ $estado }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-sm text-gray-500 text-center">
                        No hay compras registradas para este cliente.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
