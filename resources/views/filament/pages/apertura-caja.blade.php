<x-filament-panels::page>
    <x-filament::card>
        <div class="flex flex-col items-center text-center">
            
            @if ($aperturaActiva)
                {{-- Contenido si la caja YA ESTÁ abierta --}}
                
                {{-- DIV para el fondo del ícono --}}
                <div class="flex items-center justify-center w-16 h-16 mb-4 bg-success-100 rounded-full">
                    <x-heroicon-s-lock-open class="w-8 h-8 text-success-500" />
                </div>

                <h2 class="text-2xl font-bold tracking-tight">
                    Ya tienes una caja abierta
                </h2>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Tu caja fue abierta el
                    <strong>{{ $aperturaActiva->fecha_apertura->format('d/m/Y') }}</strong>
                    a las <strong>{{ $aperturaActiva->fecha_apertura->format('h:i A') }}</strong>.
                </p>

                <div class="mt-6">
                    {{-- Renderiza el botón "Ir a Facturar" --}}
                    {{ $this->getIrAFacturarAction() }}
                </div>

            @else
                {{-- Contenido si la caja ESTÁ CERRADA --}}

                {{-- DIV para el fondo del ícono --}}
                <div class="flex items-center justify-center w-16 h-16 mb-4 bg-danger-100 rounded-full">
                    <x-heroicon-s-lock-closed class="w-8 h-8 text-danger-500" />
                </div>

                <h2 class="text-2xl font-bold tracking-tight">
                    Tu caja está cerrada
                </h2>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Hola, <strong>{{ auth()->user()->name }}</strong>. Para comenzar a facturar, necesitas aperturar tu caja.
                </p>

                <div class="mt-6">
                    {{-- Renderiza el botón "Aperturar Caja" --}}
                    {{ $this->getAperturarCajaAction() }}
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament-panels::page>