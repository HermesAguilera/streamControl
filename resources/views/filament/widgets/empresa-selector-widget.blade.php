<div class="relative">
    @if($this->getEmpresas()->count() > 0)
        <div class="ml-3 relative">
            <x-filament::dropdown>
                <x-slot name="trigger">
                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition">
                        @if($currentEmpresa = $this->getCurrentEmpresa())
                            {{ $currentEmpresa->nombre }}
                        @else
                            Seleccionar Empresa
                        @endif
                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-filament::dropdown.list>
                    @foreach($this->getEmpresas() as $empresa)
                        <x-filament::dropdown.list.item 
                            :href="url()->current() . '?switch_empresa=' . $empresa->id"
                            :icon="($currentEmpresa && $currentEmpresa->id === $empresa->id) ? 'heroicon-o-check' : ''"
                            :color="($currentEmpresa && $currentEmpresa->id === $empresa->id) ? 'success' : 'gray'"
                        >
                            {{ $empresa->nombre }}
                        </x-filament::dropdown.list.item>
                    @endforeach
                </x-filament::dropdown.list>
            </x-filament::dropdown>
        </div>
    @endif
</div>
