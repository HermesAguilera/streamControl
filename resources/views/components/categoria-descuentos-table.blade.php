@php
    $descuentos = \App\Models\CategoriaClienteProducto::with('categoriaProducto')
        ->where('categoria_cliente_id', $record->id)
        ->get();
    $productosEspecificos = \App\Models\CategoriaClienteProductoEspecifico::with('producto')
        ->where('categoria_cliente_id', $record->id)
        ->get();
@endphp

<div class="space-y-6">
    {{-- Descuentos por Categoría de Producto --}}
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Descuentos por Categoría de Producto</h3>
        @if($descuentos->count() > 0)
            <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-xl">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Categoría de Producto
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Descuento
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($descuentos as $descuento)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    {{ $descuento->categoriaProducto->nombre ?? 'Categoría eliminada' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ number_format($descuento->descuento_porcentaje, 2) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($descuento->activo)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="text-gray-400 dark:text-gray-600 text-sm">
                    No hay descuentos por categoría configurados.
                </div>
            </div>
        @endif
    </div>

    {{-- Descuentos por Productos Específicos --}}
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Descuentos por Productos Específicos</h3>
        @if($productosEspecificos->count() > 0)
            <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-xl">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                SKU
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Descuento
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($productosEspecificos as $producto)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    {{ $producto->producto->nombre ?? 'Producto eliminado' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $producto->producto->sku ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ number_format($producto->descuento_porcentaje, 2) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($producto->activo)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="text-gray-400 dark:text-gray-600 text-sm">
                    No hay descuentos por productos específicos configurados.
                </div>
            </div>
        @endif
    </div>

    {{-- Botón para configurar descuentos si no hay ninguno --}}
    @if($descuentos->count() === 0 && $productosEspecificos->count() === 0)
        <div class="text-center py-8">
            <div class="text-gray-400 dark:text-gray-600 text-sm mb-3">
                No hay descuentos configurados para esta categoría de cliente.
            </div>
            <a href="{{ \App\Filament\Resources\CategoriaClienteResource::getUrl('edit', ['record' => $record]) }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-lg transition-colors">
                Configurar descuentos
            </a>
        </div>
    @endif
</div>
