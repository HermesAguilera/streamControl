<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ClientesPorPlataformaChart;
use App\Filament\Widgets\IndicadoresSectionWidget;
use App\Filament\Widgets\PlataformasOverview;
use App\Filament\Widgets\PlataformasSectionWidget;
use App\Filament\Widgets\ResumenGeneralWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel principal';
    protected static ?string $navigationIcon = 'heroicon-s-home';
    protected static ?string $navigationLabel = 'Panel principal';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->hasRole('administrador') || $user?->can('dashboard.view');
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'xl' => 2,
        ];
    }

    public function getWidgets(): array
    {
        return [
            PlataformasSectionWidget::class,
            PlataformasOverview::class,
            IndicadoresSectionWidget::class,
            ResumenGeneralWidget::class,
            ClientesPorPlataformaChart::class,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title;
    }

    public function getHeading(): string|Htmlable
    {
        return 'Bienvenido al Panel principal';
    }

    public function getSubheading(): string|Htmlable
    {
        return 'Selecciona una plataforma para gestionar registros de clientes por perfil.';
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.pages.dashboard-header', [
            'title' => $this->getHeading(),
            'subheading' => $this->getSubheading(),
        ]);
    }
}
