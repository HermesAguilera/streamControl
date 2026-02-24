<div class="w-full px-0">
    @if($empleados->isEmpty())
        <div class="text-gray-500">No hay registros de pago en el historial.</div>
    @else
        <div class="overflow-x-auto w-full">
            <table class="min-w-full w-full divide-y divide-gray-200 border">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider">Salario</th>
                        <th class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider">Deducciones</th>
                        <th class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider">Percepciones</th>
                        <th class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($empleados as $detalle)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $detalle->empleado?->persona?->primer_nombre }} {{ $detalle->empleado?->persona?->primer_apellido }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($detalle->sueldo_bruto, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($detalle->deducciones, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($detalle->percepciones, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($detalle->sueldo_neto, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button 
                                    wire:click="eliminarEmpleado('{{ $detalle->id }}')"
                                    wire:confirm="¿Está seguro que desea eliminar este empleado de la nómina?"
                                    type="button" 
                                    class="bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
    @endif
</div>
