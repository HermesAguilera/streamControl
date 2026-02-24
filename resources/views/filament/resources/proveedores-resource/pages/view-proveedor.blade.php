<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Información del Proveedor -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Información del Proveedor
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre del Proveedor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Nombre del Proveedor
                        </label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->nombre_proveedor }}
                            </p>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Teléfono
                        </label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->telefono }}
                            </p>
                        </div>
                    </div>

                    <!-- RTN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            RTN
                        </label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->rtn ?? 'No especificado' }}
                            </p>
                        </div>
                    </div>

                    <!-- Persona de Contacto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Persona de Contacto
                        </label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->persona_contacto ?? 'No especificado' }}
                            </p>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>

        <!-- Información de Ubicación -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Ubicación
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- País -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            País
                        </label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->municipio->departamento->pais->nombre_pais ?? 'No especificado' }}
                            </p>
                        </div>
                    </div>

                    <!-- Departamento -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Departamento
                        </label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->municipio->departamento->nombre_departamento ?? 'No especificado' }}
                            </p>
                        </div>
                    </div>

                    <!-- Municipio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Municipio
                        </label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                {{ $record->municipio->nombre_municipio ?? 'No especificado' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dirección -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">
                        Dirección
                    </label>
                    <div class="mt-1">
                        <p class="text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2 min-h-[80px]">
                            {{ $record->direccion ?? 'No especificado' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>