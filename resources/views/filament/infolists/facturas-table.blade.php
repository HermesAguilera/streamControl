<div class="overflow-hidden bg-white shadow rounded-lg">
    @php
        $facturas = $getRecord()->facturas ?? collect();
    @endphp
    
    @if($facturas && $facturas->count() > 0)
        <div class="px-4 py-5 sm:p-6">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    Historial de Compras - {{ $getRecord()->persona->primer_nombre ?? '' }} {{ $getRecord()->persona->primer_apellido ?? '' }}
                </h3>
                <p class="text-sm text-gray-500">Cliente: {{ $getRecord()->numero_cliente ?? 'N/A' }}</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Factura
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subtotal
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Impuestos
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Empleado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($facturas as $factura)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ str_pad($factura->id ?? 0, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($factura->fecha_factura)
                                        {{ \Carbon\Carbon::parse($factura->fecha_factura)->format('d/m/Y') }}
                                    @else
                                        <span class="text-gray-400 italic">Sin fecha</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if(isset($factura->subtotal) && $factura->subtotal > 0)
                                        L. {{ number_format($factura->subtotal, 2) }}
                                    @else
                                        <span class="text-gray-400">L. 0.00</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if(isset($factura->impuestos) && $factura->impuestos > 0)
                                        L. {{ number_format($factura->impuestos, 2) }}
                                    @else
                                        <span class="text-gray-400">L. 0.00</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(isset($factura->total) && $factura->total > 0)
                                        <span class="text-green-600 font-bold">L. {{ number_format($factura->total, 2) }}</span>
                                    @else
                                        <span class="text-red-500 font-bold">L. 0.00</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $estado = $factura->estado ?? null;
                                        if (empty($estado) || $estado === '') {
                                            $estado = 'Pendiente';
                                        }
                                        $badgeClass = match($estado) {
                                            'Pagada' => 'bg-green-100 text-green-800',
                                            'Pendiente' => 'bg-yellow-100 text-yellow-800',
                                            'Vencida' => 'bg-red-100 text-red-800',
                                            'Anulada' => 'bg-gray-100 text-gray-800',
                                            default => 'bg-blue-100 text-blue-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ $estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($factura->empleado && $factura->empleado->persona)
                                        {{ $factura->empleado->persona->primer_nombre ?? '' }} {{ $factura->empleado->persona->primer_apellido ?? '' }}
                                    @else
                                        <span class="text-gray-400 italic">Sin asignar</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase tracking-wider">
                                Total General:
                            </td>
                            <td class="px-6 py-3 text-sm font-bold text-green-600">
                                L. {{ number_format($facturas->sum('total'), 2) }}
                            </td>
                            <td colspan="2" class="px-6 py-3 text-sm text-gray-500">
                                {{ $facturas->count() }} {{ $facturas->count() === 1 ? 'factura' : 'facturas' }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Sin historial de compras</h3>
            <p class="mt-1 text-sm text-gray-500">
                Este cliente ({{ $getRecord()->persona->primer_nombre ?? '' }} {{ $getRecord()->persona->primer_apellido ?? '' }}) aún no tiene facturas registradas.
            </p>
        </div>
    @endif
</div>
