<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdminUserResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdminUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSuperAdminUser extends CreateRecord
{
    protected static string $resource = SuperAdminUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_super_admin'] = true;

        return $data;
    }
}
