<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="create">
            {{ $this->form }}
        </form>

        @if(!empty($this->productosData))
        <div class="bg-white shadow rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                    <div class="flex flex-col">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Productos a Recibir y Precios
                        </h3>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            @if($this->searchFilter)
                                Mostrando: {{ $this->getTotalProductosFiltrados() }} de {{ count($this->productosData) }} productos
                            @else
                                Total: {{ count($this->productosData) }} productos
                            @endif
                        </div>
                    </div>

                    <!-- Campo de búsqueda -->
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="searchFilter"
                                class="block w-64 pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Buscar por nombre o SKU..."
                            />
                        </div>
                        @if($this->searchFilter)
                        <button 
                            wire:click="limpiarFiltro"
                            type="button"
                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Limpiar
                        </button>
                        @endif
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Producto
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Cantidad
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Costo
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">
                                    % Ganancia
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-36">
                                    P. Detalle
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">
                                    % Mayorista
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-36">
                                    P. Mayorista
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">
                                    % Descuento
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-36">
                                    P. Promoción
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($this->getProductosPaginados() as $index => $producto)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $producto['producto_nombre'] }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{ $producto['cantidad'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    L. {{ number_format($producto['precio'], 2) }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <div class="relative w-24 mx-auto">
                                        <input 
                                            type="number" 
                                            class="block w-full pr-6 py-2.5 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-center dark:bg-gray-700 dark:border-gray-600 dark:text-white appearance-none [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none [-moz-appearance:textfield]"
                                            wire:model.live="productosData.{{ $index }}.porcentaje_ganancia"
                                            wire:change="actualizarPrecioPorPorcentaje({{ $index }}, 'porcentaje_ganancia')"
                                            min="0"
                                            step="5"
                                        />
                                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                            <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">%</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <div class="relative w-32 mx-auto">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">L.</span>
                                        </div>
                                        <input 
                                            type="number" 
                                            class="block w-full pl-8 pr-3 py-2.5 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-center appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none [-moz-appearance:textfield]"
                                            wire:model.live="productosData.{{ $index }}.precio_detalle"
                                            wire:change="actualizarPrecioManual({{ $index }}, 'precio_detalle')"
                                            min="0"
                                            step="0.01"
                                            placeholder="0.00"
                                        />
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <div class="relative w-24 mx-auto">
                                        <input 
                                            type="number" 
                                            class="block w-full pr-6 py-2.5 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-center dark:bg-gray-700 dark:border-gray-600 dark:text-white appearance-none [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none [-moz-appearance:textfield]"
                                            wire:model.live="productosData.{{ $index }}.porcentaje_ganancia_mayorista"
                                            wire:change="actualizarPrecioPorPorcentaje({{ $index }}, 'porcentaje_ganancia_mayorista')"
                                            min="0"
                                            step="5"
                                        />
                                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                            <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">%</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <div class="relative w-32 mx-auto">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">L.</span>
                                        </div>
                                        <input 
                                            type="number" 
                                            class="block w-full pl-8 pr-3 py-2.5 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-center appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none [-moz-appearance:textfield]"
                                            wire:model.live="productosData.{{ $index }}.precio_mayorista"
                                            wire:change="actualizarPrecioManual({{ $index }}, 'precio_mayorista')"
                                            min="0"
                                            step="0.01"
                                            placeholder="0.00"
                                        />
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <div class="relative w-24 mx-auto">
                                        <input 
                                            type="number" 
                                            class="block w-full pr-6 py-2.5 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-center dark:bg-gray-700 dark:border-gray-600 dark:text-white appearance-none [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none [-moz-appearance:textfield]"
                                            wire:model.live="productosData.{{ $index }}.porcentaje_descuento"
                                            wire:change="actualizarPrecioPorPorcentaje({{ $index }}, 'porcentaje_descuento')"
                                            min="0"
                                            max="100"
                                            step="5"
                                        />
                                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                            <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">%</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <div class="relative w-32 mx-auto">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">L.</span>
                                        </div>
                                        <input 
                                            type="number" 
                                            class="block w-full pl-8 pr-3 py-2.5 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-center appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none [-moz-appearance:textfield]"
                                            wire:model.live="productosData.{{ $index }}.precio_promocion"
                                            wire:change="actualizarPrecioManual({{ $index }}, 'precio_promocion')"
                                            min="0"
                                            step="0.01"
                                            placeholder="0.00"
                                        />
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mensaje cuando no hay resultados -->
                @if($this->searchFilter && $this->getTotalProductosFiltrados() == 0)
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.291-1.002-5.824-2.651M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No se encontraron productos</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        No hay productos que coincidan con "{{ $this->searchFilter }}"
                    </p>
                    <div class="mt-6">
                        <button 
                            wire:click="limpiarFiltro"
                            type="button"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Mostrar todos los productos
                        </button>
                    </div>
                </div>
                @endif

                <!-- Paginación -->
                @if($this->getTotalProductosFiltrados() > $this->perPage)
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button 
                            wire:click="previousPage"
                            @if($this->currentPage <= 1) disabled @endif
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                        >
                            Anterior
                        </button>
                        <button 
                            wire:click="nextPage"
                            @if($this->currentPage >= $this->totalPages) disabled @endif
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                        >
                            Siguiente
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Mostrando
                                <span class="font-medium">{{ (($this->currentPage - 1) * $this->perPage) + 1 }}</span>
                                a
                                <span class="font-medium">{{ min($this->currentPage * $this->perPage, $this->getTotalProductosFiltrados()) }}</span>
                                de
                                <span class="font-medium">{{ $this->getTotalProductosFiltrados() }}</span>
                                productos
                                @if($this->searchFilter)
                                    <span class="text-gray-500">(filtrados de {{ count($this->productosData) }} total)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <button 
                                    wire:click="previousPage"
                                    @if($this->currentPage <= 1) disabled @endif
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                >
                                    <span class="sr-only">Anterior</span>
                                    <!-- Heroicon name: solid/chevron-left -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                @for($i = 1; $i <= $this->totalPages; $i++)
                                    @if($i == $this->currentPage)
                                        <span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                                            {{ $i }}
                                        </span>
                                    @elseif($i == 1 || $i == $this->totalPages || ($i >= $this->currentPage - 2 && $i <= $this->currentPage + 2))
                                        <button 
                                            wire:click="goToPage({{ $i }})"
                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                        >
                                            {{ $i }}
                                        </button>
                                    @elseif($i == $this->currentPage - 3 || $i == $this->currentPage + 3)
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            ...
                                        </span>
                                    @endif
                                @endfor

                                <button 
                                    wire:click="nextPage"
                                    @if($this->currentPage >= $this->totalPages) disabled @endif
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                >
                                    <span class="sr-only">Siguiente</span>
                                    <!-- Heroicon name: solid/chevron-right -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>