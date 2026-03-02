<?php

namespace App\Filament\Resources\CuentaResource\Pages;

use App\Filament\Concerns\HasStandardCrudNotifications;
use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\CuentaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCuenta extends CreateRecord
{
    use HasStandardCrudNotifications;
    use RedirectsToResourceIndex;

    protected static string $resource = CuentaResource::class;
}
