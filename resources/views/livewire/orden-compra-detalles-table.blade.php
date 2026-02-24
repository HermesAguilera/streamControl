<div>
    @if (!empty($detallesForDisplay))
        <table class="filament-tables-table w-full text-start divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="px-4 py-2 text-start text-sm font-medium text-gray-500 dark:text-gray-400">Producto (SKU)</th>
                    <th class="px-4 py-2 text-start text-sm font-medium text-gray-500 dark:text-gray-400">Cantidad</th>
                    <th class="px-4 py-2 text-start text-sm font-medium text-gray-500 dark:text-gray-400">Precio Unitario</th>
                    <th class="px-4 py-2 text-start text-sm font-medium text-gray-500 dark:text-gray-400">Subtotal</th>
                    <th class="px-4 py-2 text-start text-sm font-medium text-gray-500 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($detallesForDisplay as $index => $detalle)
                    <tr class="@if($loop->odd) bg-white dark:bg-gray-900/60 @else bg-gray-50 dark:bg-gray-800/60 @endif">
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $detalle['producto_nombre'] ?? 'Producto no seleccionado' }} ({{ $detalle['producto_sku'] ?? 'N/A' }})
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $detalle['cantidad'] ?? 0 }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            HNL {{ number_format($detalle['precio'] ?? 0, 2) }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            HNL {{ number_format($detalle['subtotal'] ?? 0, 2) }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <button
                                type="button"
                                wire:click="removeProduct({{ $index }})"
                                wire:confirm="¿Estás seguro de que quieres eliminar este producto?"
                                class="text-danger-600 hover:text-danger-900 dark:text-danger-500 dark:hover:text-danger-400"
                            >
                                <x-filament::icon
                                    icon="heroicon-o-trash"
                                    class="h-5 w-5"
                                />
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="px-4 py-2 text-end text-sm font-bold text-gray-900 dark:text-white">
                        Total:
                    </td>
                    <td colspan="2" class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                        HNL {{ number_format(collect($detallesForDisplay)->sum('subtotal'), 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="text-gray-500 dark:text-gray-400">No hay productos añadidos a la orden. Usa los campos de arriba para agregar uno.</p>
    @endif
</div>