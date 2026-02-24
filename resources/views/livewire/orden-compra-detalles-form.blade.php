<div class="space-y-4" x-data="{ isDisabled: @entangle('isBasicInfoComplete').defer }">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($this->form->getComponents() as $component)
            @if ($component->getName() === 'producto_nombre')
                <div class="flex flex-col">
                    {{ $component }}
                </div>
            @elseif ($component->getName() === 'cantidad')
                <div class="flex flex-col">
                    {{ $component }}
                </div>
            @elseif ($component->getName() === 'precio')
                <div class="flex flex-col">
                    {{ $component }}
                </div>
            @endif
        @endforeach
    </div>

    <div class="flex justify-center mt-4">
        <button
            type="button"
            wire:click="addProducto"
            class="border border-blue-500 bg-blue-100 text-blue-800 font-medium px-6 py-2 rounded-lg shadow-sm hover:bg-blue-200 hover:text-blue-900 transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="isDisabled"
        >
            {{ isset($editIndex) && $editIndex !== null ? 'Actualizar Producto' : 'Añadir a Tabla' }}
        </button>
    </div>

    @if (count($detalles))
        <table class="w-full table-auto border mt-4">
            <thead>
                <tr class="bg-gray-100 text-left text-sm">
                    <th class="px-4 py-2">Producto</th>
                    <th class="px-4 py-2">Cantidad</th>
                    <th class="px-4 py-2">Precio</th>
                    <th class="px-4 py-2">Total</th>
                    <th class="px-4 py-2 w-64">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detalles as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $item['nombre_producto'] }}</td>
                        <td class="border px-4 py-2">{{ $item['cantidad'] }}</td>
                        <td class="border px-4 py-2">Lps {{ number_format($item['precio'], 2) }}</td>
                        <td class="border px-4 py-2">Lps {{ number_format($item['cantidad'] * $item['precio'], 2) }}</td>
                        <td class="border px-4 py-2">
                            <div class="flex flex-row gap-2">
                                <button
                                    wire:click="editDetalle({{ $index }})"
                                    type="button"
                                    class="px-3 py-1.5 bg-yellow-500 text-white text-sm rounded-md hover:bg-yellow-600 transition"
                                >
                                    ✏️ Editar
                                </button>
                                <button
                                    wire:click="removeDetalle({{ $index }})"
                                    type="button"
                                    class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition"
                                >
                                    🗑️ Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-500 mt-2">No hay productos añadidos.</p>
    @endif
</div>