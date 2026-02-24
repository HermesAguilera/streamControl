<x-filament-panels::page>
    @if ($apertura)
        {{-- Header compacto --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    
                    <p class="text-sm text-gray-500 dark:text-gray-400">Verifica y confirma el conteo del día</p>
                </div>
            </div>
            
            {{-- El botón para generar PDF se ha eliminado de aquí.
                 Filament lo renderizará automáticamente en el header de la página
                 porque lo definimos en el método getActions() de la clase. --}}
        </div>

        {{-- Dos cuadros superiores --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Cuadro 1: Monto Inicial y Ventas --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <h3 class="font-medium text-gray-900 dark:text-white">Ventas del Día</h3>
                    </div>
                </div>
                
                <div class="p-4 space-y-3">
                    {{-- Monto inicial --}}
                    <div class="flex items-center justify-between py-2 px-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-green-100 dark:bg-green-900/40 rounded flex items-center justify-center">
                                <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-green-800 dark:text-green-200">Monto Inicial</span>
                        </div>
                        <span class="font-mono font-semibold text-green-800 dark:text-green-200">
                            L {{ number_format($apertura->monto_inicial, 2) }}
                        </span>
                    </div>

                    {{-- Ventas por método --}}
                    @foreach ($reporteSistema as $metodo => $total)
                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center gap-2">
                                @if($metodo === 'Efectivo')
                                    <div class="w-6 h-6 bg-yellow-100 dark:bg-yellow-900/30 rounded flex items-center justify-center">
                                        <svg class="w-3 h-3 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2"/>
                                        </svg>
                                    </div>
                                @elseif($metodo === 'Tarjeta')
                                    <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded flex items-center justify-center">
                                        <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900/30 rounded flex items-center justify-center">
                                        <svg class="w-3 h-3 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01"/>
                                        </svg>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $metodo }}</span>
                            </div>
                            <span class="font-mono font-semibold text-gray-900 dark:text-white">
                                L {{ number_format($total, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Cuadro 2: Total Esperado --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="font-medium text-gray-900 dark:text-white">Total Esperado</h3>
                    </div>
                </div>
                
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Efectivo que debe haber en caja</p>
                    <div class="font-mono text-3xl font-bold text-blue-900 dark:text-blue-100 mb-2">
                        L {{ number_format($totalEnCajaEsperado, 2) }}
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Monto inicial + ventas en efectivo
                    </p>
                </div>
            </div>
        </div>

        {{-- Formulario abajo --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-orange-50 dark:bg-orange-900/20">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    </svg>
                    <h3 class="font-medium text-gray-900 dark:text-white">Conteo Manual de Efectivo</h3>
                </div>
            </div>
            
            <div class="p-4">
                <div class="max-w-2xl mx-auto">
                    <form wire:submit="confirmarCierre" class="grid grid-cols-2 gap-4">
                        {{ $this->form }}
                    </form>
                </div>
            </div>
        </div>
    @else
        {{-- Estado sin caja --}}
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No hay caja abierta</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Debe haber una caja abierta para realizar el cierre.
            </p>
        </div>
    @endif
</x-filament-panels::page>
