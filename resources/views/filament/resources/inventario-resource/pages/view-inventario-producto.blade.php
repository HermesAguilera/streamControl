<x-filament-panels::page>
    <div class="space-y-6">
        <div class="overflow-hidden bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                    Información del Producto
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre del Producto</label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->producto?->nombre ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->producto?->sku ?? 'No especificado' }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->producto?->unidadDeMedida?->nombre ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                    <div class="mt-1">
                        <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2 min-h-[60px]">
                            {{ $record->producto?->descripcion ?? 'No especificado' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                    Inventario y Precios
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad en Stock</label>
                        <div class="mt-1">
                            <p class="text-lg font-bold text-center text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->cantidad ?? 0 }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio Costo</label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                LPS {{ number_format($record->precio_costo ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio Venta</label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                LPS {{ number_format($record->precio_detalle ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio Oferta</label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                LPS {{ number_format($record->precio_promocion ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>