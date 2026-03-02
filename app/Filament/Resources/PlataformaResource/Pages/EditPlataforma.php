<?php

namespace App\Filament\Resources\PlataformaResource\Pages;

use App\Filament\Concerns\HasStandardCrudNotifications;
use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\PlataformaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlataforma extends EditRecord
{
    use HasStandardCrudNotifications;
    use RedirectsToResourceIndex;

    protected static string $resource = PlataformaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->modalHeading('Confirmar eliminación')
                ->modalDescription('Esta acción no se puede deshacer.')
                ->modalSubmitActionLabel('Eliminar')
                ->successNotificationTitle('Registro eliminado correctamente.'),
        ];
    }
}
