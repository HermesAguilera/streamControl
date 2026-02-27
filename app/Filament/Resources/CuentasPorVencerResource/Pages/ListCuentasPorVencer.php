<?php

namespace App\Filament\Resources\CuentasPorVencerResource\Pages;

use App\Filament\Resources\CuentasPorVencerResource;
use Filament\Resources\Pages\ListRecords;

class ListCuentasPorVencer extends ListRecords
{
    protected static string $resource = CuentasPorVencerResource::class;

    public function getTitle(): string
    {
        return 'Clientes con suscripciones por vencer';
    }

    public function getBreadcrumbs(): array
    {
        return [
            url()->route('filament.admin.pages.dashboard') => 'Dashboard',
            'Lista',
        ];
    }
}
