<?php

namespace App\Filament\SuperAdmin\Resources\TenantResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantResource;
use App\Services\Tenancy\ProvisionTenantService;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        /** @var ProvisionTenantService $provisioner */
        $provisioner = app(ProvisionTenantService::class);

        return $provisioner->provision($data, auth()->user());
    }
}
