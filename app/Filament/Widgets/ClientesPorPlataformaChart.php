<?php

namespace App\Filament\Widgets;

use App\Models\Plataforma;
use Filament\Widgets\ChartWidget;

class ClientesPorPlataformaChart extends ChartWidget
{
    protected static ?string $heading = 'Clientes por plataforma';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $plataformas = Plataforma::query()
            ->withCount([
                'perfiles as clientes_count' => fn ($query) => $query
                    ->whereNotNull('cliente_nombre')
                    ->where('cliente_nombre', '!=', ''),
            ])
            ->orderByDesc('clientes_count')
            ->orderBy('nombre')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Clientes',
                    'data' => $plataformas->pluck('clientes_count')->all(),
                    'backgroundColor' => '#6366F1',
                ],
            ],
            'labels' => $plataformas->pluck('nombre')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
