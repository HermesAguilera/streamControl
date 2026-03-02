<?php

namespace App\Filament\Concerns;

trait HasStandardCrudNotifications
{
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Registro creado correctamente.';
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Cambios guardados correctamente.';
    }
}
