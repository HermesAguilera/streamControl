<?php

namespace App\Filament\Resources\CuentaResource\Pages;

use App\Filament\Concerns\HasStandardCrudNotifications;
use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\CuentaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCuenta extends EditRecord
{
    use HasStandardCrudNotifications;
    use RedirectsToResourceIndex;

    protected static string $resource = CuentaResource::class;

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
