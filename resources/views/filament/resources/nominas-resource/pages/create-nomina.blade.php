<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="create">
            <div class="mt-2 flex justify-end">
                <x-filament::button type="submit" color="primary" icon="heroicon-o-check-circle" class="font-bold">
                    Guardar Nómina
                </x-filament::button>
            </div>
            <!-- Información general de la nómina -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                <div>
                    @if($this->isRoot)
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Empresa</label>
                        <select wire:model.live="empresa_id" class="block w-full mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white">
                            @foreach(\App\Models\Empresa::all() as $empresa)
                                <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                    @else
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Empresa</label>
                        <input type="text" class="block w-full mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white" value="{{ $this->empresaNombre ?? '' }}" disabled />
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Año</label>
                    <input type="text" class="block w-full mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white" value="{{ $this->año ?? '' }}" disabled />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Mes</label>
                    <select wire:model.live="mes" class="block w-full mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white">
                        <option value="">Seleccione un mes</option>
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                    @if(!empty($this->mostrarErrorMes) && empty($this->mes))
                        <div class="text-xs mt-1 font-semibold" style="color:#dc2626;">&#9888; Por favor seleccione un mes para la nómina.</div>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Tipo de Pago</label>
                    <select wire:model.live="tipo_pago" class="block w-full mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white">
                        <option value="mensual">Mensual</option>
                        <option value="quincenal">Quincenal</option>
                        <option value="semanal">Semanal</option>
                    </select>
                </div>
            </div>
            <div class="mb-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Descripción</label>
                <input type="text" class="block mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white w-full md:w-96 lg:w-[40rem]" wire:model.lazy="descripcion" maxlength="255" />
            </div>
            <div class="flex justify-end mt-6">
                <x-filament::button 
                    type="button" 
                    color="primary" 
                    :icon="collect($this->empleadosSeleccionados)->every(fn($e) => !empty($e['seleccionado'])) ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'" 
                    class="font-bold" 
                    wire:click="toggleSeleccionTodos">
                    @if(collect($this->empleadosSeleccionados)->every(fn($e) => !empty($e['seleccionado'])))
                        Deseleccionar todos
                    @else
                        Seleccionar todos
                    @endif
                </x-filament::button>
            </div>

            <!-- Tabla de empleados -->
            <div class="bg-white shadow rounded-lg dark:bg-gray-800 w-full">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Empleados en Nómina</h3>
                    <div class="overflow-x-auto w-full">
                        <table class="w-full min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-auto">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-center text-base font-bold uppercase tracking-wider" style="color:#1e3a8a;">Seleccionar</th>
                                    <th class="px-4 py-3 text-left text-base font-bold uppercase tracking-wider" style="color:#1e3a8a;">Nombre</th>
                                    <th class="px-4 py-3 text-center text-base font-bold uppercase tracking-wider" style="color:#1e3a8a;">Salario</th>
                                    <th class="px-4 py-3 text-center text-base font-bold uppercase tracking-wider" style="color:#1e3a8a;">Deducciones</th>
                                    <th class="px-4 py-3 text-center text-base font-bold uppercase tracking-wider" style="color:#1e3a8a;">Percepciones</th>
                                    <th class="px-4 py-3 text-center text-base font-bold uppercase tracking-wider" style="color:#1e3a8a;">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach($this->empleadosSeleccionados as $index => $empleado)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox" wire:model.live="empleadosSeleccionados.{{ $index }}.seleccionado" />
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $empleado['nombre'] }}
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-500 dark:text-gray-300">
                                        L. {{ number_format($empleado['salario'], 2) }}
                                        <!-- El tipo de pago ahora se gestiona a nivel de nómina -->
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm">
                                        <ul class="list-none p-0 m-0">
                                            @foreach($empleado['deduccionesArray'] as $dIndex => $deduccion)
                                            <li class="flex items-center gap-2">
                                                <input type="checkbox" wire:model.live="empleadosSeleccionados.{{ $index }}.deduccionesArray.{{ $dIndex }}.aplicada" />
                                                <span @if(!$deduccion['aplicada']) style="text-decoration: line-through; color: #888; font-weight: bold;" @endif>
                                                    {{ $deduccion['nombre'] }} ({{ $deduccion['valorMostrado'] }})
                                                </span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm">
                                        <ul class="list-none p-0 m-0">
                                            @foreach($empleado['percepcionesArray'] as $pIndex => $percepcion)
                                            <li class="flex items-center gap-2">
                                                <input type="checkbox" wire:model.live="empleadosSeleccionados.{{ $index }}.percepcionesArray.{{ $pIndex }}.aplicada" />
                                                <span @if(!$percepcion['aplicada']) style="text-decoration: line-through; color: #888; font-weight: bold;" @endif>
                                                    {{ $percepcion['nombre'] }} ({{ $percepcion['valorMostrado'] }})
                                                </span>
                                                <button type="button" class="ml-1 hover:scale-110 transition-transform duration-150" title="Editar valor/porcentaje"
                                                    wire:click="abrirModalEditarPercepcion({{ $index }}, {{ $pIndex }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="#facc15">
                                                        <path d="M3 17.25V21h3.75l11-11.03-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                                    </svg>
                                                </button>
                                                @if($percepcion['depende_cantidad'] ?? false)
                                                <button type="button" class="ml-1" title="Editar cantidad"
                                                    wire:click="abrirModalEditarCantidad({{ $index }}, {{ $pIndex }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#4ade80">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                </button>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm font-bold text-green-600 dark:text-green-400">
                                        <div class="flex items-center justify-center gap-2 relative">
                                            L. {{ number_format($empleado['total'], 2) }}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
            
            <!-- Modal edición percepción -->
            @if($this->modalEditarPercepcionAbierto)
                <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-xs">
                        <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Editar percepción</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Nuevo valor</label>
                            <input type="text" class="block w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-white" wire:model.defer="modalPercepcionValor" />
                        </div>
                        <div class="flex justify-end gap-2">
                            <x-filament::button color="secondary" wire:click="cerrarModalEditarPercepcion" type="button">Cancelar</x-filament::button>
                            <x-filament::button color="primary" wire:click="guardarModalEditarPercepcion" type="button">Guardar</x-filament::button>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Modal edición cantidad -->
            @if($this->modalEditarCantidadAbierto)
                <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-xs">
                        <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Editar cantidad</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Cantidad de {{ $this->modalCantidadUnidad }}</label>
                            <input type="number" min="0" step="any" class="block w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-white" wire:model.defer="modalCantidadValor" />
                        </div>
                        <div class="flex justify-end gap-2">
                            <x-filament::button color="secondary" wire:click="cerrarModalEditarCantidad" type="button">Cancelar</x-filament::button>
                            <x-filament::button color="primary" wire:click="guardarModalEditarCantidad" type="button">Guardar</x-filament::button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ...existing code... -->
            </div>
        </form>
    </div>
</x-filament-panels::page>
