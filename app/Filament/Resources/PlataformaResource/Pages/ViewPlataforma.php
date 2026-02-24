<?php

namespace App\Filament\Resources\PlataformaResource\Pages;

use App\Filament\Resources\PlataformaResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPlataforma extends ViewRecord
{
    protected static string $resource = PlataformaResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        redirect(PlataformaResource::getUrl('clientes', ['record' => $this->record]));
    }
}
