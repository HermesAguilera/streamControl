<?php

namespace App\Filament\Widgets;

use App\Models\Perfil;
use App\Models\Plataforma;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResumenGeneralWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $hoy = Carbon::today();
        $limitePorVencer = Carbon::today()->addDays(5);

        $totalClientes = Perfil::query()
            ->whereNotNull('cliente_nombre')
            ->where('cliente_nombre', '!=', '')
            ->count();

        $cuentasPorVencer = Perfil::query()
            ->whereDate('fecha_caducidad_cuenta', '>=', $hoy)
            ->whereDate('fecha_caducidad_cuenta', '<=', $limitePorVencer)
            ->count();

        $plataformasConClientes = Plataforma::query()
            ->whereHas('perfiles', function ($query) {
                $query->whereNotNull('cliente_nombre')->where('cliente_nombre', '!=', '');
            })
            ->count();

        return [
            Stat::make('Total clientes (todas las plataformas)', (string) $totalClientes)
                ->description('Indicador global principal')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Cuentas por vencer (≤5 días)', (string) $cuentasPorVencer)
                ->description('Clientes con caducidad próxima')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($cuentasPorVencer > 0 ? 'warning' : 'success'),

            Stat::make('Plataformas con clientes activos', (string) $plataformasConClientes)
                ->description('Plataformas con al menos 1 cliente')
                ->icon('heroicon-o-squares-2x2')
                ->color('gray'),
        ];
    }
}
