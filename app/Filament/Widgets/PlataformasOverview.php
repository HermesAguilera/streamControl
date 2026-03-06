<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PlataformaResource;
use App\Models\Plataforma;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Str;

class PlataformasOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $hoy = Carbon::today();
        $limitePorVencer = Carbon::today()->addDays(5);

        $plataformas = Plataforma::query()
            ->withCount([
                'perfiles as clientes_count' => fn ($query) => $query
                    ->whereNotNull('cliente_nombre')
                    ->where('cliente_nombre', '!=', ''),
                'perfiles as por_vencer_count' => fn ($query) => $query
                    ->whereDate('fecha_caducidad_cuenta', '>=', $hoy)
                    ->whereDate('fecha_caducidad_cuenta', '<=', $limitePorVencer),
            ])
            ->orderByDesc('clientes_count')
            ->orderBy('nombre')
            ->get();

        $stats = [];

        foreach ($plataformas as $plataforma) {
            $color = 'success';

            if ($plataforma->por_vencer_count > 0) {
                $color = 'warning';
            }

            if ($plataforma->clientes_count === 0) {
                $color = 'gray';
            }

            $stats[] = Stat::make(
                "Clientes: {$plataforma->clientes_count}",
                '🎬 ' . Str::upper($plataforma->nombre)
            )
                ->description("Por vencer (≤5 días): {$plataforma->por_vencer_count}")
                ->url(PlataformaResource::getUrl('clientes', ['record' => $plataforma]))
                ->color($color);
        }

        return $stats;
    }
}
