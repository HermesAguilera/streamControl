<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="generateReport" class="space-y-4">
            {{ $this->form }}

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <x-filament::button type="submit" icon="heroicon-o-arrow-path">
                    Actualizar informe
                </x-filament::button>

                <x-filament::button color="success" icon="heroicon-o-document-arrow-down" wire:click="downloadPdf">
                    Descargar PDF
                </x-filament::button>
            </div>
        </form>

        @php
            $report = $this->report;
            $resumen = $report['resumen'] ?? [];
            $rows = $report['plataformas'] ?? [];
            $moneda = $report['moneda'] ?? 'L';
        @endphp

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Clientes vendidos</div>
                <div class="mt-1 text-2xl font-semibold">{{ $resumen['vendidos'] ?? 0 }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Clientes dejados de vender</div>
                <div class="mt-1 text-2xl font-semibold text-danger-600">{{ $resumen['dejados'] ?? 0 }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Balance neto</div>
                <div class="mt-1 text-2xl font-semibold {{ ($resumen['neto'] ?? 0) >= 0 ? 'text-success-600' : 'text-danger-600' }}">{{ $resumen['neto'] ?? 0 }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Clientes activos</div>
                <div class="mt-1 text-2xl font-semibold">{{ $resumen['activos'] ?? 0 }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Retención del período</div>
                <div class="mt-1 text-2xl font-semibold">{{ $resumen['retencion'] ?? 0 }}%</div>
            </x-filament::section>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Ingreso vendido (estimado)</div>
                <div class="mt-1 text-2xl font-semibold text-success-600">{{ $moneda }} {{ number_format((float) ($resumen['ingresos_vendidos'] ?? 0), 2) }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Ingreso dejado de percibir</div>
                <div class="mt-1 text-2xl font-semibold text-danger-600">{{ $moneda }} {{ number_format((float) ($resumen['ingresos_perdidos'] ?? 0), 2) }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Balance económico neto</div>
                <div class="mt-1 text-2xl font-semibold {{ ((float) ($resumen['ingresos_netos'] ?? 0)) >= 0 ? 'text-success-600' : 'text-danger-600' }}">{{ $moneda }} {{ number_format((float) ($resumen['ingresos_netos'] ?? 0), 2) }}</div>
            </x-filament::section>
        </div>

        <x-filament::section>
            <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Detalle por plataforma</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Período {{ $report['periodo_label'] ?? '-' }}: {{ $report['start'] ?? '-' }} a {{ $report['end'] ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Ticket base (fallback): {{ $moneda }} {{ number_format((float) ($report['ticket_promedio'] ?? 0), 2) }}
                    </p>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Generado: {{ $report['generated_at'] ?? '-' }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="text-left text-sm font-medium text-gray-600 dark:text-gray-300">
                            <th class="px-3 py-2">Plataforma</th>
                            <th class="px-3 py-2 text-center">Vendidos</th>
                            <th class="px-3 py-2 text-center">Dejados</th>
                            <th class="px-3 py-2 text-center">Neto</th>
                            <th class="px-3 py-2 text-center">Ticket prom.</th>
                            <th class="px-3 py-2 text-center">Ingreso vendido</th>
                            <th class="px-3 py-2 text-center">Ingreso perdido</th>
                            <th class="px-3 py-2 text-center">Ingreso neto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm dark:divide-gray-800">
                        @forelse($rows as $row)
                            <tr>
                                <td class="px-3 py-2 font-medium">{{ $row['plataforma'] }}</td>
                                <td class="px-3 py-2 text-center">{{ $row['vendidos'] }}</td>
                                <td class="px-3 py-2 text-center">{{ $row['dejados'] }}</td>
                                <td class="px-3 py-2 text-center {{ $row['neto'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">{{ $row['neto'] }}</td>
                                <td class="px-3 py-2 text-center">{{ $moneda }} {{ number_format((float) ($row['ticket_promedio'] ?? 0), 2) }}</td>
                                <td class="px-3 py-2 text-center text-success-600">{{ $moneda }} {{ number_format((float) ($row['ingresos_vendidos'] ?? 0), 2) }}</td>
                                <td class="px-3 py-2 text-center text-danger-600">{{ $moneda }} {{ number_format((float) ($row['ingresos_perdidos'] ?? 0), 2) }}</td>
                                <td class="px-3 py-2 text-center {{ ((float) ($row['ingresos_netos'] ?? 0)) >= 0 ? 'text-success-600' : 'text-danger-600' }}">{{ $moneda }} {{ number_format((float) ($row['ingresos_netos'] ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No hay datos para el período seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
