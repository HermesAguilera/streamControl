<x-filament-panels::page>
    {{-- Header con información de la caja --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reporte de Cierre de Caja</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Caja #{{ $record->id }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Estado</div>
                    <div class="font-medium {{ $record->estado === 'CERRADA' ? 'text-red-600' : 'text-green-600' }} dark:text-white">
                        {{ $record->estado }}
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Información general (como en el PDF) --}}
        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Usuario</div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $record->user->name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Empresa</div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $record->user->empresa->nombre ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fecha de Apertura</div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $record->created_at->format('d/m/Y H:i:s') }}
                </div>
            </div>
            <div>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fecha de Cierre</div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $record->fecha_cierre?->format('d/m/Y H:i:s') ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Grid principal con los datos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Resumen de Ventas del Sistema (igual que el PDF) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="font-medium text-gray-900 dark:text-white">Resumen de Ventas del Sistema</h3>
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
                        L {{ number_format($record->monto_inicial, 2) }}
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                            @endif
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ventas {{ $metodo }}</span>
                        </div>
                        <span class="font-mono font-semibold text-gray-900 dark:text-white">
                            L {{ number_format($total, 2) }}
                        </span>
                    </div>
                @endforeach

                {{-- Total esperado en efectivo --}}
                <div class="border-t border-gray-200 dark:border-gray-600 pt-3 mt-3">
                    <div class="flex items-center justify-between py-2 px-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <span class="text-sm font-semibold text-blue-800 dark:text-blue-200">Total Esperado en Efectivo</span>
                        <span class="font-mono font-bold text-blue-900 dark:text-blue-100">
                            L {{ number_format($totalEnCajaEsperado, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comparación Sistema vs Conteo Manual (como tabla del PDF) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-orange-50 dark:bg-orange-900/20">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    </svg>
                    <h3 class="font-medium text-gray-900 dark:text-white">Comparación: Sistema vs Conteo Manual</h3>
                </div>
            </div>
            
            <div class="p-4">
                @if(!empty($diferencias))
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">
                                        Método de Pago
                                    </th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-right font-medium text-gray-700 dark:text-gray-300">
                                        Sistema
                                    </th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-right font-medium text-gray-700 dark:text-gray-300">
                                        Contado
                                    </th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-right font-medium text-gray-700 dark:text-gray-300">
                                        Diferencia
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($diferencias as $metodo => $detalle)
                                    <tr>
                                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 font-medium text-gray-900 dark:text-white">
                                            {{ $metodo }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-right font-mono text-gray-900 dark:text-white">
                                            L {{ number_format($detalle['sistema'], 2) }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-right font-mono text-gray-900 dark:text-white">
                                            L {{ number_format($detalle['contado'], 2) }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-right font-mono font-semibold 
                                            {{ $detalle['diferencia'] == 0 ? 'text-green-600' : ($detalle['diferencia'] > 0 ? 'text-green-600' : 'text-red-600') }}">
                                            @if($detalle['diferencia'] > 0)
                                                +L {{ number_format($detalle['diferencia'], 2) }}
                                            @elseif($detalle['diferencia'] < 0)
                                                L {{ number_format($detalle['diferencia'], 2) }}
                                            @else
                                                L 0.00
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Detalle del conteo manual (si existe) --}}
                    @if(!empty($conteoUsuario))
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-4">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                                Detalle del Conteo Manual
                            </h4>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                @foreach($conteoUsuario as $denominacion => $cantidad)
                                    @if($cantidad > 0)
                                        <div class="flex justify-between py-1">
                                            <span class="text-gray-600 dark:text-gray-400">{{ $denominacion }}:</span>
                                            <span class="font-mono text-gray-900 dark:text-white">{{ $cantidad }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <em>No hay datos de conteo manual disponibles</em>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Estado Final del Cierre (como en el PDF) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-indigo-50 dark:bg-indigo-900/20">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="font-medium text-gray-900 dark:text-white">Estado Final del Cierre</h3>
            </div>
        </div>
        
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Diferencia Total</span>
                    <span class="font-mono font-semibold {{ $totalDiferencias == 0 ? 'text-green-600' : ($totalDiferencias > 0 ? 'text-green-600' : 'text-red-600') }}">
                        @if($totalDiferencias > 0)
                            +L {{ number_format($totalDiferencias, 2) }} ({{ $estadoCierre }})
                        @elseif($totalDiferencias < 0)
                            L {{ number_format($totalDiferencias, 2) }} ({{ $estadoCierre }})
                        @else
                            L 0.00 ({{ $estadoCierre }})
                        @endif
                    </span>
                </div>

                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Estado del Cierre</span>
                    <span class="font-semibold {{ $estadoAprobacion === 'Aprobado' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $estadoAprobacion }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Notas del Cierre --}}
    @if($notasCierre)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                    </svg>
                    <h3 class="font-medium text-gray-900 dark:text-white">Notas del Cierre</h3>
                </div>
            </div>
            <div class="p-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border-l-4 border-blue-400">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $notasCierre }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Resumen final (como en el PDF con 4 columnas) --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-700">
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen del Día</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        L {{ number_format(array_sum($reporteSistema), 2) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Facturado</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        L {{ number_format($totalEnCajaEsperado, 2) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Efectivo Esperado</div>
                </div>
                <div class="text-center">
                    @if(!empty($conteoUsuario))
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            L {{ number_format(array_sum($conteoUsuario), 2) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Efectivo Contado</div>
                    @else
                        <div class="text-2xl font-bold text-gray-400">N/A</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Sin Conteo</div>
                    @endif
                </div>
                <div class="text-center">
                    @if(!empty($diferencias))
                        <div class="text-2xl font-bold {{ $totalDiferencias == 0 ? 'text-green-600 dark:text-green-400' : ($totalDiferencias > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400') }}">
                            @if($totalDiferencias > 0)
                                +L {{ number_format($totalDiferencias, 2) }}
                            @elseif($totalDiferencias < 0)
                                L {{ number_format($totalDiferencias, 2) }}
                            @else
                                L 0.00
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Diferencia Total</div>
                    @else
                        <div class="text-2xl font-bold text-gray-400">N/A</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Sin Diferencia</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>