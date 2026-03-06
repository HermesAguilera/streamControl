<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Concerns\HasStandardCrudNotifications;
use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    use HasStandardCrudNotifications;
    use RedirectsToResourceIndex;

    protected static string $resource = RoleResource::class;

    protected array $permissionsToSync = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissionsToSync = $data['permissions'] ?? [];
        unset($data['permissions']);

        $data['guard_name'] = 'tenant_web';

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissions($this->permissionsToSync);
    }
}
