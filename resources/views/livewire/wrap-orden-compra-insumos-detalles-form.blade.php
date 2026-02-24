<div>
    <h3 class="text-lg font-semibold">Agregar Detalles</h3>
    <div class="grid grid-cols-3 gap-4 mt-2">
        <div>
            <label class="block text-sm font-medium">Producto</label>
            <select wire:model.defer="producto_id" class="w-full border rounded p-2">
                <option value="">Seleccione un producto</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                @endforeach
            </select>
            @error('producto_id') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Cantidad</label>
            <input type="number" wire:model.defer="cantidad" class="w-full border rounded p-2" min="1">
            @error('cantidad') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Precio Unitario (HNL)</label>
            <input type="number" wire:model.defer="precio_unitario" class="w-full border rounded p-2" step="0.01" min="0">
            @error('precio_unitario') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
    </div>
    <button wire:click="addDetalle" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Agregar Detalle</button>

    @if(!empty($detalles))
        <table class="w-full mt-4 border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">Producto</th>
                    <th class="p-2 border">Cantidad</th>
                    <th class="p-2 border">Precio Unitario</th>
                    <th class="p-2 border">Subtotal</th>
                    <th class="p-2 border">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalles as $index => $detalle)
                    <tr>
                        <td class="p-2 border">{{ $productos->find($detalle['producto_id'])->nombre }}</td>
                        <td class="p-2 border">{{ $detalle['cantidad'] }}</td>
                        <td class="p-2 border">{{ number_format($detalle['precio_unitario'], 2) }} HNL</td>
                        <td class="p-2 border">{{ number_format($detalle['subtotal'], 2) }} HNL</td>
                        <td class="p-2 border">
                            <button wire:click="removeDetalle({{ $index }})" class="text-red-500">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>