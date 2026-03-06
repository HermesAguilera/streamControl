<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Concerns\HasStandardCrudNotifications;
use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use HasStandardCrudNotifications;
    use RedirectsToResourceIndex;

    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

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
