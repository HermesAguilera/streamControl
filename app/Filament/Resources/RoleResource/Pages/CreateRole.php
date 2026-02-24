<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\Empresa;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permissionsToSync = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissionsToSync = $data['permissions'] ?? [];
        unset($data['permissions']);

        $data['guard_name'] = 'web';
        $data['empresa_id'] = auth()->user()?->empresa_id ?? Empresa::query()->value('id');

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissions($this->permissionsToSync);
    }
}
